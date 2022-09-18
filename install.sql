-- add column in user table
ALTER TABLE wcf1_user ADD jCoinsProducts INT(10) DEFAULT 0;

-- shop
DROP TABLE IF EXISTS wcf1_jcoins_shop_item;
CREATE TABLE wcf1_jcoins_shop_item (
    shopItemID                INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    changeTime                INT(10) NOT NULL DEFAULT 0,
    sender                    VARCHAR(255) NOT NULL,
    leaveConversation        TINYINT(1) NOT NULL DEFAULT 0,
    time                    INT(10) NOT NULL DEFAULT 0,
    expirationStatus        TINYINT(1) NOT NULL DEFAULT 0,
    expirationDate            INT(10) NOT NULL DEFAULT 0,
    isDisabled                TINYINT(1) NOT NULL DEFAULT 0,
    isMultilingual            TINYINT(1) NOT NULL DEFAULT 0,
    itemTitle                VARCHAR(80) NOT NULL,
    showStartPage            TINYINT(1) NOT NULL DEFAULT 1,
    sortOrder                INT(10) NOT NULL DEFAULT 0,
    typeID                    INT(10),
    typeDes                    VARCHAR(20),
    filename                VARCHAR(255) NOT NULL,
    handsonNames            TEXT NOT NULL,
    membershipID            INT(10),
    membershipDays            INT(10),
    membershipWarn            INT(10),
    textItem                MEDIUMTEXT,
    textItemAutoLimit        TINYINT(1) NOT NULL DEFAULT 1,
    trophyID                INT(10) DEFAULT NULL,
    price                    INT(10) NOT NULL,
    isOffer                    TINYINT(1) NOT NULL DEFAULT 0,
    offerEnd                INT(10) NOT NULL DEFAULT 0,
    offerPrice                INT(10) NOT NULL DEFAULT 0,
    buyLimit                INT(10) NOT NULL DEFAULT 0,
    productLimit            INT(10) NOT NULL DEFAULT 0,
    sold                    INT(10) NOT NULL DEFAULT 0,
    earnings                INT(10) NOT NULL DEFAULT 0,

    autoDisable                TINYINT(1) NOT NULL DEFAULT 0,

    KEY (changeTime),
    KEY (sortOrder),
    KEY (price),
    KEY (sold),
    KEY (autoDisable)
);

DROP TABLE IF EXISTS wcf1_jcoins_shop_item_content;
CREATE TABLE wcf1_jcoins_shop_item_content (
    contentID                INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    hasEmbeddedObjects        TINYINT(1) NOT NULL DEFAULT 0,
    shopItemID                INT(10),
    languageID                INT(10),
    content                    MEDIUMTEXT,
    subject                    VARCHAR(255) DEFAULT '',
    teaser                    TEXT,
    imageID                    INT(10),

    UNIQUE KEY (shopItemID, languageID)
);

DROP TABLE IF EXISTS wcf1_jcoins_shop_item_buyer;
CREATE TABLE wcf1_jcoins_shop_item_buyer (
    shopItemID                INT(10) NOT NULL,
    userID                    INT(10) NOT NULL,
    buyDate                    INT(10) NOT NULL DEFAULT 0,
    price                    INT(10) NOT NULL DEFAULT 0,

    KEY (shopItemID, userID)
);

DROP TABLE IF EXISTS wcf1_jcoins_shop_transaction;
CREATE TABLE wcf1_jcoins_shop_transaction (
    transactionID            INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    shopItemID                INT(10) NOT NULL,
    itemTitle                VARCHAR(80) NOT NULL,
    detail                    VARCHAR(255) NOT NULL DEFAULT '',
    typeDes                    VARCHAR(20),
    price                    INT(10) NOT NULL,
    time                    INT(10) NOT NULL DEFAULT 0,
    userID                    INT(10) NOT NULL,
    username                VARCHAR(255) NOT NULL DEFAULT '',
);

DROP TABLE IF EXISTS wcf1_jcoins_shop_membership;
CREATE TABLE wcf1_jcoins_shop_membership (
    membershipID            INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    shopItemID                INT(10) NOT NULL,
    groupID                    INT(10) NOT NULL,
    userID                    INT(10) NOT NULL,
    endDate                    INT(10) NOT NULL DEFAULT 0,
    isActive                TINYINT(1) NOT NULL DEFAULT 1,
    startDate                INT(10) NOT NULL DEFAULT 0,
    warnDate                INT(10) NOT NULL DEFAULT 0,
    isWarned                TINYINT(1) NOT NULL DEFAULT 0
);


DROP TABLE IF EXISTS wcf1_jcoins_shop_item_type;
CREATE TABLE wcf1_jcoins_shop_item_type (
    id                        INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    typeID                    INT(10) NOT NULL DEFAULT 0,
    typeTitle                VARCHAR(30) NOT NULL DEFAULT '',
    expires                    TINYINT(1) NOT NULL DEFAULT 0,
    multiple                TINYINT(1) DEFAULT 0,
    notify                    TINYINT(1) DEFAULT 0,
    packageID                INT(10) NOT NULL,
    source                    TINYINT(1) DEFAULT 0,
    sortOrder                INT(10) NOT NULL,

    UNIQUE KEY (typeID),
    KEY (sortOrder)
);

DROP TABLE IF EXISTS wcf1_jcoins_shop_item_to_category;
CREATE TABLE wcf1_jcoins_shop_item_to_category (
    categoryID                INT(10) NOT NULL,
    shopItemID                INT(10) NOT NULL,
    PRIMARY KEY (categoryID, shopItemID)
);

-- foreign keys
-- item
ALTER TABLE wcf1_jcoins_shop_item ADD FOREIGN KEY (membershipID) REFERENCES wcf1_user_group (groupID) ON DELETE SET NULL;
ALTER TABLE wcf1_jcoins_shop_item ADD FOREIGN KEY (typeID) REFERENCES wcf1_jcoins_shop_item_type (typeID) ON DELETE SET NULL;
ALTER TABLE wcf1_jcoins_shop_item ADD FOREIGN KEY (trophyID) REFERENCES wcf1_trophy (trophyID) ON DELETE SET NULL;

-- category
ALTER TABLE wcf1_jcoins_shop_item_to_category ADD FOREIGN KEY (categoryID) REFERENCES wcf1_category (categoryID) ON DELETE CASCADE;
ALTER TABLE wcf1_jcoins_shop_item_to_category ADD FOREIGN KEY (shopItemID) REFERENCES wcf1_jcoins_shop_item (shopItemID) ON DELETE CASCADE;

-- membership
ALTER TABLE wcf1_jcoins_shop_membership ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE CASCADE;
ALTER TABLE wcf1_jcoins_shop_membership ADD FOREIGN KEY (shopItemID) REFERENCES wcf1_jcoins_shop_item (shopItemID) ON DELETE CASCADE;
ALTER TABLE wcf1_jcoins_shop_membership ADD FOREIGN KEY (groupID) REFERENCES wcf1_user_group (groupID) ON DELETE CASCADE;

-- transaction
ALTER TABLE wcf1_jcoins_shop_transaction ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE CASCADE;
ALTER TABLE wcf1_jcoins_shop_transaction ADD FOREIGN KEY (shopItemID) REFERENCES wcf1_jcoins_shop_item (shopItemID) ON DELETE CASCADE;

-- type
ALTER TABLE wcf1_jcoins_shop_item_type ADD FOREIGN KEY (packageID) REFERENCES wcf1_package (packageID) ON DELETE CASCADE;

-- content
ALTER TABLE wcf1_jcoins_shop_item_content ADD FOREIGN KEY (shopItemID) REFERENCES wcf1_jcoins_shop_item (shopItemID) ON DELETE CASCADE;
ALTER TABLE wcf1_jcoins_shop_item_content ADD FOREIGN KEY (languageID) REFERENCES wcf1_language (languageID) ON DELETE SET NULL;
ALTER TABLE wcf1_jcoins_shop_item_content ADD FOREIGN KEY (imageID) REFERENCES wcf1_media (mediaID) ON DELETE SET NULL;

-- buyer
ALTER TABLE wcf1_jcoins_shop_item_buyer ADD FOREIGN KEY (shopItemID) REFERENCES wcf1_jcoins_shop_item (shopItemID) ON DELETE CASCADE;
ALTER TABLE wcf1_jcoins_shop_item_buyer ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE CASCADE;
