--
-- NIE (Null If Empty)
--
DROP FUNCTION IF EXISTS NIE;
CREATE FUNCTION NIE (str TEXT)
RETURNS TEXT
DETERMINISTIC
RETURN IF(TRIM(str) = "", NULL, IF(TRIM(str) = "0", NULL, TRIM(str)));

--
-- NEW_TOKEN
--
DROP FUNCTION IF EXISTS `NEW_TOKEN`;
DELIMITER $$
CREATE FUNCTION `NEW_TOKEN` ()
RETURNS CHAR(8)
NOT DETERMINISTIC
BEGIN
DECLARE i TINYINT(3) DEFAULT 1;
DECLARE len TINYINT(3) DEFAULT 8;
DECLARE buf CHAR(8) DEFAULT "";
DECLARE last_char, next_char CHAR(1) DEFAULT "";
DECLARE alpha CHAR(36) DEFAULT "abcdefghijklmnopqrstuvwxyz1234567890";
DECLARE aplha_len TINYINT(3) DEFAULT LENGTH(alpha);
WHILE (i <= len) DO
    -- prevent repeating chars
    WHILE (next_char = last_char) DO
        SET next_char = SUBSTRING(alpha, FLOOR(RAND()*aplha_len)+1, 1);
    END WHILE;
    SET buf = CONCAT(buf, next_char);
    SET last_char = next_char;
    SET i = i+1;
END WHILE;
RETURN buf;
END $$
DELIMITER ;


--
-- CREATE_PRIZE
--
DROP PROCEDURE IF EXISTS `CREATE_PRIZE`;
DELIMITER $$
CREATE PROCEDURE `CREATE_PRIZE` (
    IN  IN_TITLE        VARCHAR(255),
    IN  IN_IMG1         VARCHAR(255),
    IN  IN_DESC1        VARCHAR(512),
    IN  IN_IMG2         VARCHAR(255),
    IN  IN_DESC2        VARCHAR(512),
    IN  IN_IMG3         VARCHAR(255),
    IN  IN_DESC3        VARCHAR(512),
    IN  IN_AWARD        VARCHAR(255),
    IN  IN_VALUE        SMALLINT(5) UNSIGNED,
    IN  IN_TYPE         VARCHAR(255)
)
BEGIN
    INSERT INTO `prize` (
        `title`,
        `img1`,
        `desc1`,
        `img2`,
        `desc2`,
        `img3`,
        `desc3`,
        `award`,
        `value`,
        `type`
    )
    VALUES (
        IN_TITLE,
        IF(IN_IMG1 = "", NULL, UNHEX(IN_IMG1)),
        IN_DESC1,
        IF(IN_IMG2 = "", NULL, UNHEX(IN_IMG2)),
        IN_DESC2,
        IF(IN_IMG3 = "", NULL, UNHEX(IN_IMG3)),
        IN_DESC3,
        IN_AWARD,
        IN_VALUE,
        IN_TYPE
    );
    SELECT LAST_INSERT_ID() AS `prize_id`;
END $$
DELIMITER ;


--
-- UPDATE_PRIZE
--
DROP PROCEDURE IF EXISTS `UPDATE_PRIZE`;
DELIMITER $$
CREATE PROCEDURE `UPDATE_PRIZE` (
    IN  IN_ID           SMALLINT(5) UNSIGNED,
    IN  IN_TITLE        VARCHAR(255),
    IN  IN_IMG1         VARCHAR(255),
    IN  IN_DESC1        VARCHAR(512),
    IN  IN_IMG2         VARCHAR(255),
    IN  IN_DESC2        VARCHAR(512),
    IN  IN_IMG3         VARCHAR(255),
    IN  IN_DESC3        VARCHAR(512),
    IN  IN_AWARD        VARCHAR(255),
    IN  IN_VALUE        SMALLINT(5) UNSIGNED,
    IN  IN_TYPE         VARCHAR(255)
)
UPDATE_PRIZE_SPROC:BEGIN

    -- if this prize has `contest`.`date`s in the past,
    -- then `award`, `value`, and `type` are immutable
    DECLARE immutable TINYINT(1);
    SELECT IF(MIN(`c`.`date`) <= DATE(NOW()), 1, 0) INTO immutable
    FROM `contest` `c`
    WHERE `prize_id` = IN_ID;

    IF immutable = 1 THEN
        UPDATE `prize`
        SET
            `title` = IF(IN_TITLE = "", NULL, IN_TITLE),
            `img1`  = IF(IN_IMG1  = "", NULL, UNHEX(IN_IMG1)),
            `desc1` = IF(IN_DESC1 = "", NULL, IN_DESC1),
            `img2`  = IF(IN_IMG2  = "", NULL, UNHEX(IN_IMG2)),
            `desc2` = IF(IN_DESC2 = "", NULL, IN_DESC2),
            `img3`  = IF(IN_IMG3  = "", NULL, UNHEX(IN_IMG3)),
            `desc3` = IF(IN_DESC3 = "", NULL, IN_DESC3)
        WHERE
            `id` = IN_ID;
    ELSE
        UPDATE `prize`
        SET
            `title` = IF(IN_TITLE = "", NULL, IN_TITLE),
            `img1`  = IF(IN_IMG1  = "", NULL, UNHEX(IN_IMG1)),
            `desc1` = IF(IN_DESC1 = "", NULL, IN_DESC1),
            `img2`  = IF(IN_IMG2  = "", NULL, UNHEX(IN_IMG2)),
            `desc2` = IF(IN_DESC2 = "", NULL, IN_DESC2),
            `img3`  = IF(IN_IMG3  = "", NULL, UNHEX(IN_IMG3)),
            `desc3` = IF(IN_DESC3 = "", NULL, IN_DESC3),
            `award` = IN_AWARD,
            `value` = IN_VALUE,
            `type`  = IN_TYPE
        WHERE
            `id` = IN_ID;
    END IF;

    -- 1: prize updated
    -- 2: successful, but no updates
    SELECT IF(ROW_COUNT()=1, 1, 2) AS `success`;
END $$
DELIMITER ;


--
-- PICK_WINNER
--
DROP PROCEDURE IF EXISTS `PICK_WINNER`;
DELIMITER $$
CREATE PROCEDURE `PICK_WINNER` (
    IN  IN_DATE     DATE
)
PICK_WINNER_SPROC:BEGIN
    -- POSSIBLE return values
    -- (int) -1: Contest (IN_DATE) does not exist
    -- (int) -2: (IN_DATE) does not have any entries
    -- `user` row of the new winner

    DECLARE existing_contest TINYINT(1) DEFAULT NULL;
    DECLARE existing_winner_id,
            new_winner_user_id,
            new_winner_site_id SMALLINT(5) DEFAULT NULL;

    -- check if the contest date exists and if a winner has already been chosen
    SELECT
        1,
        IF(`winner_user_id` IS NULL, 0, `winner_user_id`)
    INTO
        existing_contest,
        existing_winner_id
    FROM
        `contest`
    WHERE
        `date` = IN_DATE;

    -- if no contest is set for the date, return -1
    IF existing_contest IS NULL THEN
        -- (int) -1: Contest (IN_DATE) does not exist
        SELECT -1 AS `error`;
        LEAVE PICK_WINNER_SPROC;
    END IF;

    -- let's pick a winner...
    SELECT
        `user_id`,
        `site_id`
    INTO
        new_winner_user_id,
        new_winner_site_id
    FROM
        `entry`
    WHERE
        `date` = IN_DATE
    -- make sure we're not repicking the existing winner
    AND
        `user_id` != existing_winner_id
    -- make sure they haven't won within 30 day time frame
    AND
        `user_id` NOT IN (
            SELECT
                DISTINCT(`winner_user_id`)
            FROM
                `contest`
            WHERE
                `date` BETWEEN DATE_SUB(IN_DATE, INTERVAL 30 DAY) AND DATE_ADD(IN_DATE, INTERVAL 30 DAY)
            AND
                `winner_user_id` IS NOT NULL
            )
    ORDER BY
        RAND()
    LIMIT
        1;

    -- I guess we weren't able to pick a winner
    IF new_winner_user_id IS NULL THEN
        -- (int) -2: (IN_DATE) does not have any entries
        SELECT -2 AS `error`;
        LEAVE PICK_WINNER_SPROC;
    END IF;

    -- all is good, update the contest table with our new winner
    UPDATE
        `contest`
    SET
        `winner_user_id` = new_winner_user_id,
        `winner_site_id` = new_winner_site_id
    WHERE
        `date` = IN_DATE;

    -- get the user info and return it
    SELECT
        `id`,
        `firstname`,
        `lastname`,
        `email`,
        `city`,
        `state`
    FROM
        `user`
    WHERE
        `id` = new_winner_user_id;

END $$
DELIMITER ;


--
-- LOGIN
--
DROP PROCEDURE IF EXISTS `LOGIN`;
DELIMITER $$
CREATE PROCEDURE `LOGIN` (
    IN  IN_EMAIL    VARCHAR(255),
    IN  IN_PASSWORD VARCHAR(255)
)
LOGIN_SPROC:BEGIN
    -- RETURN:
    -- `user` row of the correct user

    DECLARE SALT CHAR(40) DEFAULT "4b3be7d16b2875f2d4153a67c23e0d2586585c67";

    DECLARE MY_ID INT(11) UNSIGNED;
    DECLARE MY_ROLE TINYINT(3) UNSIGNED;
    DECLARE MY_FIRSTNAME VARCHAR(255);
    DECLARE MY_PASSWORD BINARY(20);

    SELECT
        `id`,
        `role`,
        `firstname`,
        `password`
    INTO
        MY_ID,
        MY_ROLE,
        MY_FIRSTNAME,
        MY_PASSWORD
    FROM
        `user`
    WHERE
        `email` = IN_EMAIL;

    IF MY_ID IS NULL THEN
        -- user row not found
        SELECT 'USER_NOT_FOUND' AS `error`;
        LEAVE LOGIN_SPROC;
    END IF;

    IF MY_PASSWORD <> UNHEX(SHA1(CONCAT(IN_PASSWORD,SALT))) THEN
        -- invalid password
        SELECT 'INVALID_PASSWORD' AS `error`;
        LEAVE LOGIN_SPROC;
    END IF;

    SELECT
        MY_ID AS `id`,
        MY_ROLE AS `role`,
        MY_FIRSTNAME AS `firstname`;

END $$
DELIMITER ;

--
-- CREATE_USER
--
DROP PROCEDURE IF EXISTS `CREATE_USER`;
DELIMITER $$
CREATE PROCEDURE `CREATE_USER` (
    IN  IN_IP           VARCHAR(15),    -- will be converted with INET_ATON()
    IN  IN_OPTIN        TINYINT(1),
    IN  IN_SITE_ID      TINYINT(3),
    IN  IN_EMAIL        VARCHAR(70),
    IN  IN_PASSWORD     VARCHAR(255),   -- will get SHA1 SALT'd
    IN  IN_FIRSTNAME    VARCHAR(255),
    IN  IN_LASTNAME     VARCHAR(255),
    IN  IN_ADDRESS      VARCHAR(255),
    IN  IN_CITY         VARCHAR(255),
    IN  IN_STATE        CHAR(2),
    IN  IN_ZIP          MEDIUMINT(5) UNSIGNED
)
CREATE_USER_SPROC:BEGIN
    -- RETURN:
    -- -1 on duplicate email
    --  0 on failure
    -- `user_id` on success

    DECLARE SALT CHAR(40) DEFAULT "4b3be7d16b2875f2d4153a67c23e0d2586585c67";

    -- must explicitely set `date_registered` since MariaDB does not support
    -- 'DEFAULT CURRENT_TIMESTAMP' with 'ON UPDATE CURRENT_TIMESTAMP'
    -- on a different column (`date_updated`)
    INSERT IGNORE INTO
        `user`
    SET
        `ip`              = INET_ATON(IN_IP),
        `optin`           = IN_OPTIN,
        `site_id`         = IN_SITE_ID,
        `date_registered` = NOW(),
        `email`           = IN_EMAIL,
        `password`        = UNHEX(SHA1(CONCAT(IN_PASSWORD,SALT))),
        `firstname`       = IN_FIRSTNAME,
        `lastname`        = IN_LASTNAME,
        `address`         = IN_ADDRESS,
        `city`            = IN_CITY,
        `state`           = IN_STATE,
        `zip`             = IN_ZIP;

    SELECT IF(ROW_COUNT()=1, LAST_INSERT_ID(), -1) AS `result`;

END $$
DELIMITER ;

--
-- UPDATE_USER
--
DROP PROCEDURE IF EXISTS `UPDATE_USER`;
DELIMITER $$
CREATE PROCEDURE `UPDATE_USER` (
    IN  IN_ID           MEDIUMINT(8) UNSIGNED,
    IN  IN_EMAIL        VARCHAR(70),
    IN  IN_PASSWORD     VARCHAR(255),   -- will get SHA1 SALT'd
    -- IN  IN_IP           VARCHAR(15), -- only set the IP on registration, for now
    IN  IN_FIRSTNAME    VARCHAR(255),
    IN  IN_LASTNAME     VARCHAR(255),
    IN  IN_ADDRESS      VARCHAR(255),
    IN  IN_CITY         VARCHAR(255),
    IN  IN_STATE        CHAR(2),
    IN  IN_ZIP          MEDIUMINT(5) UNSIGNED
)
UPDATE_USER_SPROC:BEGIN
    -- RETURN:
    -- -2 user does not exist
    -- -1 on duplicate email
    --  0 on failure
    --  1 on new email
    --  2 on success with changes
    --  3 on success, but no changes

    DECLARE SALT        CHAR(40)    DEFAULT "4b3be7d16b2875f2d4153a67c23e0d2586585c67";
    DECLARE RETURN_VAL  TINYINT(1)  SIGNED;
    DECLARE OLD_EMAIL   VARCHAR(70);
    DECLARE OLD_VER     TINYINT(1)  UNSIGNED;
    DECLARE DATE_VER    TIMESTAMP;

    -- return -1 if email address is already registered with another user
    DECLARE EXIT HANDLER FOR 1062 SELECT -1 AS `result`;

    -- grab current email and verification information for comparison
    SELECT
        `email`,
        `verified`,
        `date_verified`
    INTO
        OLD_EMAIL,
        OLD_VER,
        DATE_VER
    FROM
        `user`
    WHERE
        `id` = IN_ID;

    IF OLD_EMAIL IS NULL THEN
        -- -2: user does not exist
        SELECT -2 AS `result`;
        LEAVE UPDATE_USER_SPROC;
    END IF;

    IF OLD_EMAIL = IN_EMAIL OR NIE(IN_EMAIL) IS NULL THEN
        SET RETURN_VAL = 2;
    ELSE
        SET RETURN_VAL = 1;
        SET OLD_VER = 0;
        SET DATE_VER = NULL;
    END IF;

    -- perform update
    UPDATE
        `user`
    SET
        `email`         = IF(NIE(IN_EMAIL)     IS NULL, `email`,     NIE(IN_EMAIL)),
        `password`      = IF(NIE(IN_PASSWORD)  IS NULL, `password`,  UNHEX(SHA1(CONCAT(IN_PASSWORD,SALT)))),
        -- `ip`            = IF(NIE(IN_IP)        IS NULL, `ip`,        INET_ATON(IN_IP)),
        `firstname`     = IF(NIE(IN_FIRSTNAME) IS NULL, `firstname`, NIE(IN_FIRSTNAME)),
        `lastname`      = IF(NIE(IN_LASTNAME)  IS NULL, `lastname`,  NIE(IN_LASTNAME)),
        `address`       = IF(NIE(IN_ADDRESS)   IS NULL, `address`,   NIE(IN_ADDRESS)),
        `city`          = IF(NIE(IN_CITY)      IS NULL, `city`,      NIE(IN_CITY)),
        `state`         = IF(NIE(IN_STATE)     IS NULL, `state`,     NIE(IN_STATE)),
        `zip`           = IF(NIE(IN_ZIP)       IS NULL, `zip`,       NIE(IN_ZIP)),
        `verified`      = OLD_VER,
        `date_verified` = DATE_VER
    WHERE
        `id` = IN_ID;

    SELECT IF(ROW_COUNT()=1, RETURN_VAL, 3) AS `result`;

END $$
DELIMITER ;

--
-- RESET
--
DROP PROCEDURE IF EXISTS `RESET`;
DELIMITER $$
CREATE PROCEDURE `RESET` (
    IN  IN_TOKEN        CHAR(8),
    IN  IN_PASSWORD     VARCHAR(255),
    IN  IN_TTL          INT(11) UNSIGNED
)
RESET_SPROC:BEGIN
    -- RETURN:
    -- `result` 1 on success
    -- `result` 0 on failure

    DECLARE SALT        CHAR(40)                DEFAULT "4b3be7d16b2875f2d4153a67c23e0d2586585c67";
    DECLARE THIS_ID     MEDIUMINT(5) UNSIGNED;
    DECLARE THIS_EMAIL  VARCHAR(70);
    DECLARE THIS_TTL    INT(11)      UNSIGNED   DEFAULT IN_TTL;

    -- define a TTL if NULL
    IF IN_TTL IS NULL THEN
        SET THIS_TTL = 86400;
    END IF;

    -- match this token up with `user`
    SELECT
        `u`.`id`,
        `u`.`email`
    INTO
        THIS_ID,
        THIS_EMAIL
    FROM
        `reset` AS `r`
    LEFT JOIN
        `user` AS `u` ON (`u`.`email` = `r`.`email`)
    WHERE
        `token` = IN_TOKEN
    AND
        `timestamp` > DATE_SUB(NOW(), INTERVAL THIS_TTL SECOND)
    AND
        `type` = "reset";

    IF THIS_ID IS NULL THEN
        SELECT 0 AS `result`;
        LEAVE RESET_SPROC;
    END IF;

    -- remove any and all tokens for this email address
    DELETE FROM `reset` WHERE `email` = THIS_EMAIL;

    UPDATE
        `user`
    SET
        `password` = UNHEX(SHA1(CONCAT(IN_PASSWORD,SALT))),
        `verified` = 1,
        `date_verified` = NOW()
    WHERE
        `id` = THIS_ID;

    SELECT IF(ROW_COUNT()=1, 1, 0) AS `result`;

END $$
DELIMITER ;

--
-- VERIFY
--
DROP PROCEDURE IF EXISTS `VERIFY`;
DELIMITER $$
CREATE PROCEDURE `VERIFY` (
    IN  IN_TOKEN        CHAR(8),
    IN  IN_TTL          INT(11) UNSIGNED
)
VERIFY_SPROC:BEGIN
    -- RETURN:
    -- `result` 1 on success
    -- `result` 0 on failure

    DECLARE CHECK_EMAIL VARCHAR(70);
    DECLARE THIS_TTL    INT(11) UNSIGNED DEFAULT IN_TTL;

    IF IN_TTL IS NULL THEN
        SET THIS_TTL = 86400;
    END IF;

    -- find the email address for this token
    SELECT
        `email`
    INTO
        CHECK_EMAIL
    FROM
        `reset`
    WHERE
        `token` = IN_TOKEN
    AND
        `timestamp` > DATE_SUB(NOW(), INTERVAL THIS_TTL SECOND)
    AND
        `type` = "verify";

    IF CHECK_EMAIL IS NULL THEN
        SELECT 0 AS `result`;
        LEAVE VERIFY_SPROC;
    END IF;

    -- remove any and all tokens for this email address
    DELETE FROM `reset` WHERE `email` = CHECK_EMAIL;

    -- set the user's email address as verified
    UPDATE
        `user`
    SET
        `verified` = 1,
        `date_verified` = NOW()
    WHERE
        `email` = CHECK_EMAIL;

    SELECT IF(ROW_COUNT()=1, 1, 0) AS `result`;

END $$
DELIMITER ;

--
-- RESET_TOKEN
--
DROP PROCEDURE IF EXISTS `RESET_TOKEN`;
DELIMITER $$
CREATE PROCEDURE `RESET_TOKEN` (
    IN  IN_EMAIL        VARCHAR(70)
)
RESET_TOKEN_SPROC:BEGIN
    -- RETURN:
    -- `token` CHAR(8) on success
    -- NULL on error or if email does not exist in `user` table

    DECLARE THIS_TOKEN CHAR(8) DEFAULT NEW_TOKEN();
    DECLARE CHECK_EMAIL VARCHAR(70);

    -- return NULL if we happen to get a duplicate token
    DECLARE EXIT HANDLER FOR 1062 SELECT NULL AS `token`;

    SELECT `email` INTO CHECK_EMAIL FROM `user` WHERE `email` = IN_EMAIL;

    -- bail if email does not exist in `user` table
    IF CHECK_EMAIL IS NULL THEN
        SELECT NULL AS `token`;
        LEAVE RESET_TOKEN_SPROC;
    END IF;

    -- delete any pre-existing reset tokens for this email address
    DELETE FROM `reset` WHERE `email` = IN_EMAIL;

    INSERT INTO
        `reset`
    SET
        `token` = THIS_TOKEN,
        `email` = IN_EMAIL,
        `type`  = "reset";

    SELECT THIS_TOKEN AS `token`;

END $$
DELIMITER ;

--
-- VERIFY_TOKEN
--
DROP PROCEDURE IF EXISTS `VERIFY_TOKEN`;
DELIMITER $$
CREATE PROCEDURE `VERIFY_TOKEN` (
    IN  IN_ID           MEDIUMINT(8) UNSIGNED
)
VERIFY_TOKEN_SPROC:BEGIN
    -- RETURN:
    -- `token` CHAR(8), `email` VARCHAR(70) on success
    -- NULL, NULL on error or if email does not exist in `user` table

    DECLARE THIS_TOKEN CHAR(8) DEFAULT NEW_TOKEN();
    DECLARE CHECK_EMAIL VARCHAR(70);

    -- return NULL if we happen to get a duplicate token
    DECLARE EXIT HANDLER FOR 1062 SELECT NULL AS `token`, NULL AS `email`;

    SELECT `email` INTO CHECK_EMAIL FROM `user` WHERE `id` = IN_ID;

    -- bail if email does not exist in `user` table
    IF CHECK_EMAIL IS NULL THEN
        SELECT NULL AS `token`, NULL as `email`;
        LEAVE VERIFY_TOKEN_SPROC;
    END IF;

    -- delete any pre-existing reset tokens for this email address
    DELETE FROM `reset` WHERE `email` = CHECK_EMAIL;

    INSERT INTO
        `reset`
    SET
        `token` = THIS_TOKEN,
        `email` = CHECK_EMAIL,
        `type`  = "verify";

    SELECT THIS_TOKEN AS `token`, CHECK_EMAIL AS `email`;

END $$
DELIMITER ;
