CREATE TABLE $TABLE_NAME (
ID bigint (20) unsigned NOT NULL AUTO_INCREMENT,
student_id bigint (20) unsigned NOT NULL,
cart text,
status varchar(255) NOT NULL DEFAULT 'payment_incomplete',
updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY  (ID)
) $COLLATE;