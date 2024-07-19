CREATE TABLE $TABLE_NAME (
ID bigint (20) unsigned NOT NULL AUTO_INCREMENT,
course_id bigint (20) unsigned NOT NULL,
student_id bigint (20) unsigned NOT NULL,
order_id bigint (20) unsigned NOT NULL,
schedule varchar(255) NOT NULL,
passed tinyint(1),
updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY  (ID)
) $COLLATE;