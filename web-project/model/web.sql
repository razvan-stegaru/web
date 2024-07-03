
DROP TABLE users CASCADE CONSTRAINTS;
DROP TABLE pwdReset CASCADE CONSTRAINTS;
DROP TABLE posts CASCADE CONSTRAINTS;


DROP SEQUENCE users_seq;
DROP SEQUENCE posts_seq;


CREATE SEQUENCE users_seq START WITH 1 INCREMENT BY 1;


CREATE SEQUENCE posts_seq START WITH 1 INCREMENT BY 1;

CREATE TABLE users (
    usersID NUMBER(11) PRIMARY KEY,
    usersFirstName VARCHAR2(128) NOT NULL,
    usersLastName VARCHAR2(128) NOT NULL,
    usersEmail VARCHAR2(128) NOT NULL,
    usersPwd VARCHAR2(128) NOT NULL
);

CREATE OR REPLACE TRIGGER users_trigger
BEFORE INSERT ON users
FOR EACH ROW
BEGIN
    :new.usersID := users_seq.NEXTVAL;
END;
/

CREATE TABLE posts (
    usersID NUMBER(11) NOT NULL,
    postID NUMBER(11) PRIMARY KEY,
    postName VARCHAR2(128) NOT NULL,
    postSubject VARCHAR2(4000),
    CONSTRAINT posts_fk FOREIGN KEY (usersID) REFERENCES users(usersID)
);


CREATE OR REPLACE TRIGGER posts_trigger
BEFORE INSERT ON posts
FOR EACH ROW
BEGIN 
    :new.postID := posts_seq.NEXTVAL;
END;
/

CREATE SEQUENCE pwdReset_seq
    START WITH 1
    INCREMENT BY 1
    NOMAXVALUE;

CREATE TABLE pwdReset (
    pwdResetID NUMBER PRIMARY KEY,
    pwdResetEmail VARCHAR2(255) NOT NULL,
    pwdResetSelector VARCHAR2(255) NOT NULL,
    pwdResetToken VARCHAR2(255) NOT NULL,
    pwdResetExpires TIMESTAMP NOT NULL
);
SELECT sequence_name
FROM all_sequences
WHERE sequence_name = 'PWDRESET_SEQ';
DROP SEQUENCE pwdReset_seq;
-- SELECT * FROM users;
