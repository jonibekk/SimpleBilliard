# インデックスの調査

## select count(*)

### team_id ×, del_flg ×
```sql
mysql> select SQL_NO_CACHE count(*) from `myapp`.`posts` AS `Post` IGNORE INDEX (team_id,del_flg) where `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0';
+----------+
| count(*) |
+----------+
|  3000000 |
+----------+
1 row in set (2.33 sec)

mysql> explain select SQL_NO_CACHE count(*) from `myapp`.`posts` AS `Post` IGNORE INDEX (team_id,del_flg) where `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0';
+----+-------------+-------+------+---------------+------+---------+------+---------+-------------+
| id | select_type | table | type | possible_keys | key  | key_len | ref  | rows    | Extra       |
+----+-------------+-------+------+---------------+------+---------+------+---------+-------------+
|  1 | SIMPLE      | Post  | ALL  | modified      | NULL | NULL    | NULL | 6003217 | Using where |
+----+-------------+-------+------+---------------+------+---------+------+---------+-------------+
1 row in set (0.01 sec)

mysql>
```

### team_id ×, del_flg ○
```sql
mysql> select SQL_NO_CACHE count(*) from `myapp`.`posts` AS `Post` IGNORE INDEX (team_id) where `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0';
+----------+
| count(*) |
+----------+
|  3000000 |
+----------+
1 row in set (16.95 sec)

mysql> explain select SQL_NO_CACHE count(*) from `myapp`.`posts` AS `Post` IGNORE INDEX (team_id) where `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0';
+----+-------------+-------+------+------------------+---------+---------+-------+---------+-------------+
| id | select_type | table | type | possible_keys    | key     | key_len | ref   | rows    | Extra       |
+----+-------------+-------+------+------------------+---------+---------+-------+---------+-------------+
|  1 | SIMPLE      | Post  | ref  | del_flg,modified | del_flg | 1       | const | 3001609 | Using where |
+----+-------------+-------+------+------------------+---------+---------+-------+---------+-------------+
1 row in set (0.02 sec)

mysql>

```

### team_id ○, del_flg ×
```sql
mysql> select SQL_NO_CACHE count(*) from `myapp`.`posts` AS `Post` IGNORE INDEX (del_flg) where `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0';
+----------+
| count(*) |
+----------+
|  3000000 |
+----------+
1 row in set (3.33 sec)

mysql> explain select SQL_NO_CACHE count(*) from `myapp`.`posts` AS `Post` IGNORE INDEX (del_flg) where `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0';
+----+-------------+-------+------+------------------+---------+---------+-------+---------+-------------+
| id | select_type | table | type | possible_keys    | key     | key_len | ref   | rows    | Extra       |
+----+-------------+-------+------+------------------+---------+---------+-------+---------+-------------+
|  1 | SIMPLE      | Post  | ref  | team_id,modified | team_id | 8       | const | 3001609 | Using where |
+----+-------------+-------+------+------------------+---------+---------+-------+---------+-------------+
1 row in set (0.00 sec)

mysql>
```

### team_id ○, del_flg ○
```sql
mysql> select SQL_NO_CACHE count(*) from `myapp`.`posts` AS `Post` where `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0';
+----------+
| count(*) |
+----------+
|  1000000 |
+----------+
1 row in set (2.90 sec)

mysql> explain select SQL_NO_CACHE count(*) from `myapp`.`posts` AS `Post` where `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0';
+----+-------------+-------+-------------+--------------------------+-----------------+---------+------+---------+------------------------------------------------------------+
| id | select_type | table | type        | possible_keys            | key             | key_len | ref  | rows    | Extra                                                      |
+----+-------------+-------+-------------+--------------------------+-----------------+---------+------+---------+------------------------------------------------------------+
|  1 | SIMPLE      | Post  | index_merge | team_id,del_flg,modified | del_flg,team_id | 1,8     | NULL | 1500804 | Using intersect(del_flg,team_id); Using where; Using index |
+----+-------------+-------+-------------+--------------------------+-----------------+---------+------+---------+------------------------------------------------------------+
1 row in set (0.01 sec)
mysql> explain partitions  select SQL_NO_CACHE count(*) from `myapp`.`posts` AS `Post`  where `Post`.`team_id` = 1 AND `Post`.`modified` BETWEEN 1397524918 AND 1405387316  and `Post`.`del_flg` = '0';
+----+-------------+-------+---------------------------------+-------------+--------------------------+-----------------+---------+------+---------+------------------------------------------------------------+
| id | select_type | table | partitions                      | type        | possible_keys            | key             | key_len | ref  | rows    | Extra                                                      |
+----+-------------+-------+---------------------------------+-------------+--------------------------+-----------------+---------+------+---------+------------------------------------------------------------+
|  1 | SIMPLE      | Post  | p201404,p201405,p201406,p201407 | index_merge | team_id,del_flg,modified | del_flg,team_id | 1,8     | NULL | 1500804 | Using intersect(del_flg,team_id); Using where; Using index |
+----+-------------+-------+---------------------------------+-------------+--------------------------+-----------------+---------+------+---------+------------------------------------------------------------+
1 row in set (0.00 sec)

mysql>
```



## select

### team_id ×, del_flg ×
```sql
mysql> SELECT SQL_NO_CACHE `Post`.`id`, `Post`.`user_id`, `Post`.`body`, `Post`.`type`, `Post`.`comment_count`, `Post`.`post_like_count`, `Post`.`post_read_count`, `Post`.`public_flg`, `Post`.`important_flg`, `Post`.`goal_id`,`Post`.`created`, `Post`.`modified`, `User`.`id`, `User`.`photo_file_name`, `User`.`timezone`, `User`.`language`, `User`.`auto_language_flg`, `User`.`romanize_flg`, `User`.`first_name`, `User`.`last_name`, `User`.`middle_name`,  (CONCAT(`User`.`first_name`, " ", `User`.`last_name`)) AS `User__username` FROM `myapp`.`posts` AS `Post` IGNORE INDEX (team_id,del_flg) LEFT JOIN `myapp`.`users` AS `User` ON (`Post`.`user_id` = `User`.`id` AND `User`.`del_flg` = '0') WHERE `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0' ORDER BY `Post`.`modified` desc LIMIT 20;
+---------+---------+-------------------+------+---------------+-----------------+-----------------+------------+---------------+---------+------------+------------+------+-------------------+----------+----------+-------------------+--------------+------------+-----------+-------------+----------------+
| id      | user_id | body              | type | comment_count | post_like_count | post_read_count | public_flg | important_flg | goal_id | created    | modified   | id   | photo_file_name   | timezone | language | auto_language_flg | romanize_flg | first_name | last_name | middle_name | User__username |
+---------+---------+-------------------+------+---------------+-----------------+-----------------+------------+---------------+---------+------------+------------+------+-------------------+----------+----------+-------------------+--------------+------------+-----------+-------------+----------------+
| 1000000 |       1 | test_text82911354 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475265 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999999 |       1 | test_text82911353 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475266 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999998 |       1 | test_text82911352 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475267 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999997 |       1 | test_text82911351 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475268 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999996 |       1 | test_text82911350 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475269 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999995 |       1 | test_text82911349 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475270 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999994 |       1 | test_text82911348 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475271 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999993 |       1 | test_text82911347 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475272 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999992 |       1 | test_text82911346 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475273 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999991 |       1 | test_text82911345 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475274 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999990 |       1 | test_text82911344 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475275 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999989 |       1 | test_text82911343 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475276 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999988 |       1 | test_text82911342 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475277 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999987 |       1 | test_text82911341 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475278 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999986 |       1 | test_text82911340 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475279 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999985 |       1 | test_text82911339 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475280 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999984 |       1 | test_text82911338 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475281 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999983 |       1 | test_text82911337 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475282 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999982 |       1 | test_text82911336 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475283 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999981 |       1 | test_text82911335 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475284 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
+---------+---------+-------------------+------+---------------+-----------------+-----------------+------------+---------------+---------+------------+------------+------+-------------------+----------+----------+-------------------+--------------+------------+-----------+-------------+----------------+
20 rows in set (1.51 sec)

mysql> explain SELECT SQL_NO_CACHE `Post`.`id`, `Post`.`user_id`, `Post`.`body`, `Post`.`type`, `Post`.`comment_count`, `Post`.`post_like_count`, `Post`.`post_read_count`, `Post`.`public_flg`, `Post`.`important_flg`, `Post`.`goal_id`,`Post`.`created`, `Post`.`modified`, `User`.`id`, `User`.`photo_file_name`, `User`.`timezone`, `User`.`language`, `User`.`auto_language_flg`, `User`.`romanize_flg`, `User`.`first_name`, `User`.`last_name`, `User`.`middle_name`,  (CONCAT(`User`.`first_name`, " ", `User`.`last_name`)) AS `User__username` FROM `myapp`.`posts` AS `Post` IGNORE INDEX (team_id,del_flg) LEFT JOIN `myapp`.`users` AS `User` ON (`Post`.`user_id` = `User`.`id` AND `User`.`del_flg` = '0') WHERE `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0' ORDER BY `Post`.`modified` desc LIMIT 20;
+----+-------------+-------+--------+-----------------+----------+---------+--------------------+---------+-------------+
| id | select_type | table | type   | possible_keys   | key      | key_len | ref                | rows    | Extra       |
+----+-------------+-------+--------+-----------------+----------+---------+--------------------+---------+-------------+
|  1 | SIMPLE      | Post  | range  | modified        | modified | 4       | NULL               | 3001609 | Using where |
|  1 | SIMPLE      | User  | eq_ref | PRIMARY,del_flg | PRIMARY  | 8       | myapp.Post.user_id |       1 |             |
+----+-------------+-------+--------+-----------------+----------+---------+--------------------+---------+-------------+
2 rows in set (0.01 sec)

mysql>
```

### team_id ×, del_flg ○
```sql
mysql> SELECT SQL_NO_CACHE `Post`.`id`, `Post`.`user_id`, `Post`.`body`, `Post`.`type`, `Post`.`comment_count`, `Post`.`post_like_count`, `Post`.`post_read_count`, `Post`.`public_flg`, `Post`.`important_flg`, `Post`.`goal_id`,`Post`.`created`, `Post`.`modified`, `User`.`id`, `User`.`photo_file_name`, `User`.`timezone`, `User`.`language`, `User`.`auto_language_flg`, `User`.`romanize_flg`, `User`.`first_name`, `User`.`last_name`, `User`.`middle_name`,  (CONCAT(`User`.`first_name`, " ", `User`.`last_name`)) AS `User__username` FROM `myapp`.`posts` AS `Post` IGNORE INDEX (team_id) LEFT JOIN `myapp`.`users` AS `User` ON (`Post`.`user_id` = `User`.`id` AND `User`.`del_flg` = '0') WHERE `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0' ORDER BY `Post`.`modified` desc LIMIT 20;
+---------+---------+-------------------+------+---------------+-----------------+-----------------+------------+---------------+---------+------------+------------+------+-------------------+----------+----------+-------------------+--------------+------------+-----------+-------------+----------------+
| id      | user_id | body              | type | comment_count | post_like_count | post_read_count | public_flg | important_flg | goal_id | created    | modified   | id   | photo_file_name   | timezone | language | auto_language_flg | romanize_flg | first_name | last_name | middle_name | User__username |
+---------+---------+-------------------+------+---------------+-----------------+-----------------+------------+---------------+---------+------------+------------+------+-------------------+----------+----------+-------------------+--------------+------------+-----------+-------------+----------------+
| 1000000 |       1 | test_text82911354 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475265 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999999 |       1 | test_text82911353 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475266 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999998 |       1 | test_text82911352 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475267 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999997 |       1 | test_text82911351 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475268 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999996 |       1 | test_text82911350 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475269 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999995 |       1 | test_text82911349 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475270 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999994 |       1 | test_text82911348 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475271 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999993 |       1 | test_text82911347 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475272 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999992 |       1 | test_text82911346 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475273 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999991 |       1 | test_text82911345 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475274 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999990 |       1 | test_text82911344 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475275 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999989 |       1 | test_text82911343 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475276 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999988 |       1 | test_text82911342 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475277 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999987 |       1 | test_text82911341 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475278 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999986 |       1 | test_text82911340 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475279 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999985 |       1 | test_text82911339 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475280 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999984 |       1 | test_text82911338 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475281 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999983 |       1 | test_text82911337 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475282 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999982 |       1 | test_text82911336 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475283 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999981 |       1 | test_text82911335 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475284 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
+---------+---------+-------------------+------+---------------+-----------------+-----------------+------------+---------------+---------+------------+------------+------+-------------------+----------+----------+-------------------+--------------+------------+-----------+-------------+----------------+
20 rows in set (1.69 sec)

mysql> explain SELECT SQL_NO_CACHE `Post`.`id`, `Post`.`user_id`, `Post`.`body`, `Post`.`type`, `Post`.`comment_count`, `Post`.`post_like_count`, `Post`.`post_read_count`, `Post`.`public_flg`, `Post`.`important_flg`, `Post`.`goal_id`,`Post`.`created`, `Post`.`modified`, `User`.`id`, `User`.`photo_file_name`, `User`.`timezone`, `User`.`language`, `User`.`auto_language_flg`, `User`.`romanize_flg`, `User`.`first_name`, `User`.`last_name`, `User`.`middle_name`,  (CONCAT(`User`.`first_name`, " ", `User`.`last_name`)) AS `User__username` FROM `myapp`.`posts` AS `Post` IGNORE INDEX (team_id) LEFT JOIN `myapp`.`users` AS `User` ON (`Post`.`user_id` = `User`.`id` AND `User`.`del_flg` = '0') WHERE `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0' ORDER BY `Post`.`modified` desc LIMIT 20;
+----+-------------+-------+--------+------------------+----------+---------+--------------------+---------+-------------+
| id | select_type | table | type   | possible_keys    | key      | key_len | ref                | rows    | Extra       |
+----+-------------+-------+--------+------------------+----------+---------+--------------------+---------+-------------+
|  1 | SIMPLE      | Post  | range  | del_flg,modified | modified | 4       | NULL               | 3001609 | Using where |
|  1 | SIMPLE      | User  | eq_ref | PRIMARY,del_flg  | PRIMARY  | 8       | myapp.Post.user_id |       1 |             |
+----+-------------+-------+--------+------------------+----------+---------+--------------------+---------+-------------+
2 rows in set (0.00 sec)

mysql>

```

### team_id ○, del_flg ×
```sql
mysql> SELECT SQL_NO_CACHE `Post`.`id`, `Post`.`user_id`, `Post`.`body`, `Post`.`type`, `Post`.`comment_count`, `Post`.`post_like_count`, `Post`.`post_read_count`, `Post`.`public_flg`, `Post`.`important_flg`, `Post`.`goal_id`,`Post`.`created`, `Post`.`modified`, `User`.`id`, `User`.`photo_file_name`, `User`.`timezone`, `User`.`language`, `User`.`auto_language_flg`, `User`.`romanize_flg`, `User`.`first_name`, `User`.`last_name`, `User`.`middle_name`,  (CONCAT(`User`.`first_name`, " ", `User`.`last_name`)) AS `User__username` FROM `myapp`.`posts` AS `Post` IGNORE INDEX (del_flg) LEFT JOIN `myapp`.`users` AS `User` ON (`Post`.`user_id` = `User`.`id` AND `User`.`del_flg` = '0') WHERE `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0' ORDER BY `Post`.`modified` desc LIMIT 20;
+---------+---------+-------------------+------+---------------+-----------------+-----------------+------------+---------------+---------+------------+------------+------+-------------------+----------+----------+-------------------+--------------+------------+-----------+-------------+----------------+
| id      | user_id | body              | type | comment_count | post_like_count | post_read_count | public_flg | important_flg | goal_id | created    | modified   | id   | photo_file_name   | timezone | language | auto_language_flg | romanize_flg | first_name | last_name | middle_name | User__username |
+---------+---------+-------------------+------+---------------+-----------------+-----------------+------------+---------------+---------+------------+------------+------+-------------------+----------+----------+-------------------+--------------+------------+-----------+-------------+----------------+
| 1000000 |       1 | test_text82911354 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475265 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999999 |       1 | test_text82911353 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475266 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999998 |       1 | test_text82911352 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475267 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999997 |       1 | test_text82911351 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475268 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999996 |       1 | test_text82911350 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475269 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999995 |       1 | test_text82911349 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475270 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999994 |       1 | test_text82911348 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475271 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999993 |       1 | test_text82911347 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475272 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999992 |       1 | test_text82911346 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475273 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999991 |       1 | test_text82911345 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475274 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999990 |       1 | test_text82911344 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475275 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999989 |       1 | test_text82911343 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475276 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999988 |       1 | test_text82911342 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475277 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999987 |       1 | test_text82911341 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475278 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999986 |       1 | test_text82911340 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475279 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999985 |       1 | test_text82911339 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475280 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999984 |       1 | test_text82911338 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475281 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999983 |       1 | test_text82911337 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475282 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999982 |       1 | test_text82911336 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475283 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999981 |       1 | test_text82911335 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475284 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
+---------+---------+-------------------+------+---------------+-----------------+-----------------+------------+---------------+---------+------------+------------+------+-------------------+----------+----------+-------------------+--------------+------------+-----------+-------------+----------------+
20 rows in set (1.49 sec)

mysql> explain SELECT SQL_NO_CACHE `Post`.`id`, `Post`.`user_id`, `Post`.`body`, `Post`.`type`, `Post`.`comment_count`, `Post`.`post_like_count`, `Post`.`post_read_count`, `Post`.`public_flg`, `Post`.`important_flg`, `Post`.`goal_id`,`Post`.`created`, `Post`.`modified`, `User`.`id`, `User`.`photo_file_name`, `User`.`timezone`, `User`.`language`, `User`.`auto_language_flg`, `User`.`romanize_flg`, `User`.`first_name`, `User`.`last_name`, `User`.`middle_name`,  (CONCAT(`User`.`first_name`, " ", `User`.`last_name`)) AS `User__username` FROM `myapp`.`posts` AS `Post` IGNORE INDEX (del_flg) LEFT JOIN `myapp`.`users` AS `User` ON (`Post`.`user_id` = `User`.`id` AND `User`.`del_flg` = '0') WHERE `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0' ORDER BY `Post`.`modified` desc LIMIT 20;
+----+-------------+-------+--------+------------------+----------+---------+--------------------+---------+-------------+
| id | select_type | table | type   | possible_keys    | key      | key_len | ref                | rows    | Extra       |
+----+-------------+-------+--------+------------------+----------+---------+--------------------+---------+-------------+
|  1 | SIMPLE      | Post  | range  | team_id,modified | modified | 4       | NULL               | 3001609 | Using where |
|  1 | SIMPLE      | User  | eq_ref | PRIMARY,del_flg  | PRIMARY  | 8       | myapp.Post.user_id |       1 |             |
+----+-------------+-------+--------+------------------+----------+---------+--------------------+---------+-------------+
2 rows in set (0.00 sec)

mysql>
```

### team_id ○, del_flg ○
```sql
ysql> SELECT SQL_NO_CACHE `Post`.`id`, `Post`.`user_id`, `Post`.`body`, `Post`.`type`, `Post`.`comment_count`, `Post`.`post_like_count`, `Post`.`post_read_count`, `Post`.`public_flg`, `Post`.`important_flg`, `Post`.`goal_id`,`Post`.`created`, `Post`.`modified`, `User`.`id`, `User`.`photo_file_name`, `User`.`timezone`, `User`.`language`, `User`.`auto_language_flg`, `User`.`romanize_flg`, `User`.`first_name`, `User`.`last_name`, `User`.`middle_name`,  (CONCAT(`User`.`first_name`, " ", `User`.`last_name`)) AS `User__username` FROM `myapp`.`posts` AS `Post` LEFT JOIN `myapp`.`users` AS `User` ON (`Post`.`user_id` = `User`.`id` AND `User`.`del_flg` = '0') WHERE `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0' ORDER BY `Post`.`modified` desc LIMIT 20;
+---------+---------+-------------------+------+---------------+-----------------+-----------------+------------+---------------+---------+------------+------------+------+-------------------+----------+----------+-------------------+--------------+------------+-----------+-------------+----------------+
| id      | user_id | body              | type | comment_count | post_like_count | post_read_count | public_flg | important_flg | goal_id | created    | modified   | id   | photo_file_name   | timezone | language | auto_language_flg | romanize_flg | first_name | last_name | middle_name | User__username |
+---------+---------+-------------------+------+---------------+-----------------+-----------------+------------+---------------+---------+------------+------------+------+-------------------+----------+----------+-------------------+--------------+------------+-----------+-------------+----------------+
| 1000000 |       1 | test_text82911354 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475265 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999999 |       1 | test_text82911353 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475266 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999998 |       1 | test_text82911352 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475267 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999997 |       1 | test_text82911351 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475268 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999996 |       1 | test_text82911350 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475269 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999995 |       1 | test_text82911349 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475270 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999994 |       1 | test_text82911348 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475271 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999993 |       1 | test_text82911347 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475272 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999992 |       1 | test_text82911346 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475273 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999991 |       1 | test_text82911345 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475274 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999990 |       1 | test_text82911344 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475275 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999989 |       1 | test_text82911343 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475276 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999988 |       1 | test_text82911342 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475277 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999987 |       1 | test_text82911341 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475278 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999986 |       1 | test_text82911340 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475279 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999985 |       1 | test_text82911339 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475280 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999984 |       1 | test_text82911338 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475281 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999983 |       1 | test_text82911337 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475282 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999982 |       1 | test_text82911336 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475283 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
|  999981 |       1 | test_text82911335 |    1 |             1 |               1 |               1 |          1 |             1 |       0 | 1322475284 | 1404226800 |    1 | DSC0151mini_2.jpg |        9 | jpn      |                 0 |            0 | DAIKI      | HIRAKATA  | NULL        | DAIKI HIRAKATA |
+---------+---------+-------------------+------+---------------+-----------------+-----------------+------------+---------------+---------+------------+------------+------+-------------------+----------+----------+-------------------+--------------+------------+-----------+-------------+----------------+
20 rows in set (1.49 sec)

mysql>
mysql>
mysql>
mysql>
mysql>
mysql> explain SELECT SQL_NO_CACHE `Post`.`id`, `Post`.`user_id`, `Post`.`body`, `Post`.`type`, `Post`.`comment_count`, `Post`.`post_like_count`, `Post`.`post_read_count`, `Post`.`public_flg`, `Post`.`important_flg`, `Post`.`goal_id`,`Post`.`created`, `Post`.`modified`, `User`.`id`, `User`.`photo_file_name`, `User`.`timezone`, `User`.`language`, `User`.`auto_language_flg`, `User`.`romanize_flg`, `User`.`first_name`, `User`.`last_name`, `User`.`middle_name`,  (CONCAT(`User`.`first_name`, " ", `User`.`last_name`)) AS `User__username` FROM `myapp`.`posts` AS `Post` LEFT JOIN `myapp`.`users` AS `User` ON (`Post`.`user_id` = `User`.`id` AND `User`.`del_flg` = '0') WHERE `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0' ORDER BY `Post`.`modified` desc LIMIT 20;
+----+-------------+-------+--------+--------------------------+----------+---------+--------------------+---------+-------------+
| id | select_type | table | type   | possible_keys            | key      | key_len | ref                | rows    | Extra       |
+----+-------------+-------+--------+--------------------------+----------+---------+--------------------+---------+-------------+
|  1 | SIMPLE      | Post  | range  | team_id,del_flg,modified | modified | 4       | NULL               | 3001609 | Using where |
|  1 | SIMPLE      | User  | eq_ref | PRIMARY,del_flg          | PRIMARY  | 8       | myapp.Post.user_id |       1 |             |
+----+-------------+-------+--------+--------------------------+----------+---------+--------------------+---------+-------------+
2 rows in set (0.00 sec)

mysql>
```

# パーティショニング

```sql
### インデックスなし


mysql> #測定クエリその１(過去３ヶ月の投稿データ取得)
mysql> SELECT SQL_NO_CACHE `Post`.`id`, `Post`.`user_id`, `Post`.`body`, `Post`.`type`, `Post`.`comment_count`, `Post`.`post_like_count`, `Post`.`post_read_count`, `Post`.`public_flg`, `Post`.`important_flg`, `Post`.`goal_id`,`Post`.`created`, `Post`.`modified`, `User`.`id`, `User`.`photo_file_name`, `User`.`timezone`, `User`.`language`, `User`.`auto_language_flg`, `User`.`romanize_flg`, `User`.`first_name`, `User`.`last_name`, `User`.`middle_name`,  (CONCAT(`User`.`first_name`, " ", `User`.`last_name`)) AS `User__username` FROM `myapp`.`posts` AS `Post` LEFT JOIN `myapp`.`users` AS `User` ON (`Post`.`user_id` = `User`.`id` AND `User`.`del_flg` = '0') WHERE `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0' ORDER BY `Post`.`modified` desc LIMIT 20;
#結果省略

20 rows in set (1 min 12.18 sec)

mysql>
mysql> #EXPLAIN
mysql> EXPLAIN SELECT SQL_NO_CACHE `Post`.`id`, `Post`.`user_id`, `Post`.`body`, `Post`.`type`, `Post`.`comment_count`, `Post`.`post_like_count`, `Post`.`post_read_count`, `Post`.`public_flg`, `Post`.`important_flg`, `Post`.`goal_id`,`Post`.`created`, `Post`.`modified`, `User`.`id`, `User`.`photo_file_name`, `User`.`timezone`, `User`.`language`, `User`.`auto_language_flg`, `User`.`romanize_flg`, `User`.`first_name`, `User`.`last_name`, `User`.`middle_name`,  (CONCAT(`User`.`first_name`, " ", `User`.`last_name`)) AS `User__username` FROM `myapp`.`posts` AS `Post` LEFT JOIN `myapp`.`users` AS `User` ON (`Post`.`user_id` = `User`.`id` AND `User`.`del_flg` = '0') WHERE `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0' ORDER BY `Post`.`modified` desc LIMIT 20;
+----+-------------+-------+--------+-----------------+---------+---------+--------------------+-----------+-----------------------------+
| id | select_type | table | type   | possible_keys   | key     | key_len | ref                | rows      | Extra                       |
+----+-------------+-------+--------+-----------------+---------+---------+--------------------+-----------+-----------------------------+
|  1 | SIMPLE      | Post  | ALL    | NULL            | NULL    | NULL    | NULL               | 100627275 | Using where; Using filesort |
|  1 | SIMPLE      | User  | eq_ref | PRIMARY,del_flg | PRIMARY | 8       | myapp.Post.user_id |         1 |                             |
+----+-------------+-------+--------+-----------------+---------+---------+--------------------+-----------+-----------------------------+
2 rows in set (0.02 sec)

mysql>
mysql>
mysql> #測定クエリその２(過去３ヶ月の投稿データのカウント)
mysql> select SQL_NO_CACHE count(*) from `myapp`.`posts` AS `Post` where `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0';
+----------+
| count(*) |
+----------+
|  3000000 |
+----------+
1 row in set (1 min 2.37 sec)

mysql>
mysql> #EXPLAIN
mysql> EXPLAIN select SQL_NO_CACHE count(*) from `myapp`.`posts` AS `Post` where `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0';
+----+-------------+-------+------+---------------+------+---------+------+-----------+-------------+
| id | select_type | table | type | possible_keys | key  | key_len | ref  | rows      | Extra       |
+----+-------------+-------+------+---------------+------+---------+------+-----------+-------------+
|  1 | SIMPLE      | Post  | ALL  | NULL          | NULL | NULL    | NULL | 100627275 | Using where |
+----+-------------+-------+------+---------------+------+---------+------+-----------+-------------+
1 row in set (0.02 sec)

mysql>


### team_idのインデックス追加

mysql> #測定クエリその１(過去３ヶ月の投稿データ取得)
mysql> SELECT SQL_NO_CACHE `Post`.`id`, `Post`.`user_id`, `Post`.`body`, `Post`.`type`, `Post`.`comment_count`, `Post`.`post_like_count`, `Post`.`post_read_count`, `Post`.`public_flg`, `Post`.`important_flg`, `Post`.`goal_id`,`Post`.`created`, `Post`.`modified`, `User`.`id`, `User`.`photo_file_name`, `User`.`timezone`, `User`.`language`, `User`.`auto_language_flg`, `User`.`romanize_flg`, `User`.`first_name`, `User`.`last_name`, `User`.`middle_name`,  (CONCAT(`User`.`first_name`, " ", `User`.`last_name`)) AS `User__username` FROM `myapp`.`posts` AS `Post` LEFT JOIN `myapp`.`users` AS `User` ON (`Post`.`user_id` = `User`.`id` AND `User`.`del_flg` = '0') WHERE `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0' ORDER BY `Post`.`modified` desc LIMIT 20;
#結果省略

20 rows in set (4.21 sec)

mysql>

mysql> #EXPLAIN
mysql> EXPLAIN SELECT SQL_NO_CACHE `Post`.`id`, `Post`.`user_id`, `Post`.`body`, `Post`.`type`, `Post`.`comment_count`, `Post`.`post_like_count`, `Post`.`post_read_count`, `Post`.`public_flg`, `Post`.`important_flg`, `Post`.`goal_id`,`Post`.`created`, `Post`.`modified`, `User`.`id`, `User`.`photo_file_name`, `User`.`timezone`, `User`.`language`, `User`.`auto_language_flg`, `User`.`romanize_flg`, `User`.`first_name`, `User`.`last_name`, `User`.`middle_name`,  (CONCAT(`User`.`first_name`, " ", `User`.`last_name`)) AS `User__username` FROM `myapp`.`posts` AS `Post` LEFT JOIN `myapp`.`users` AS `User` ON (`Post`.`user_id` = `User`.`id` AND `User`.`del_flg` = '0') WHERE `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0' ORDER BY `Post`.`modified` desc LIMIT 20;
+----+-------------+-------+--------+-------------------+---------+---------+--------------------+---------+-----------------------------+
| id | select_type | table | type   | possible_keys     | key     | key_len | ref                | rows    | Extra                       |
+----+-------------+-------+--------+-------------------+---------+---------+--------------------+---------+-----------------------------+
|  1 | SIMPLE      | Post  | ref    | team_id,team_id_2 | team_id | 8       | const              | 5744038 | Using where; Using filesort |
|  1 | SIMPLE      | User  | eq_ref | PRIMARY,del_flg   | PRIMARY | 8       | myapp.Post.user_id |       1 |                             |
+----+-------------+-------+--------+-------------------+---------+---------+--------------------+---------+-----------------------------+
2 rows in set (0.00 sec)


mysql> select SQL_NO_CACHE count(*) from `myapp`.`posts` AS `Post` where `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0';
+----------+
| count(*) |
+----------+
|  3000000 |
+----------+
1 row in set (3.10 sec)

mysql>



mysql> #EXPLAIN
mysql> EXPLAIN select SQL_NO_CACHE count(*) from `myapp`.`posts` AS `Post` where `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0';
+----+-------------+-------+------+-------------------+---------+---------+-------+---------+-------------+
| id | select_type | table | type | possible_keys     | key     | key_len | ref   | rows    | Extra       |
+----+-------------+-------+------+-------------------+---------+---------+-------+---------+-------------+
|  1 | SIMPLE      | Post  | ref  | team_id,team_id_2 | team_id | 8       | const | 5744038 | Using where |
+----+-------------+-------+------+-------------------+---------+---------+-------+---------+-------------+
1 row in set (0.00 sec)



### del_flgにインデックス付与
mysql> #測定クエリその１(過去３ヶ月の投稿データ取得)
mysql> SELECT SQL_NO_CACHE `Post`.`id`, `Post`.`user_id`, `Post`.`body`, `Post`.`type`, `Post`.`comment_count`, `Post`.`post_like_count`, `Post`.`post_read_count`, `Post`.`public_flg`, `Post`.`important_flg`, `Post`.`goal_id`,`Post`.`created`, `Post`.`modified`, `User`.`id`, `User`.`photo_file_name`, `User`.`timezone`, `User`.`language`, `User`.`auto_language_flg`, `User`.`romanize_flg`, `User`.`first_name`, `User`.`last_name`, `User`.`middle_name`,  (CONCAT(`User`.`first_name`, " ", `User`.`last_name`)) AS `User__username` FROM `myapp`.`posts` AS `Post` LEFT JOIN `myapp`.`users` AS `User` ON (`Post`.`user_id` = `User`.`id` AND `User`.`del_flg` = '0') WHERE `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0' ORDER BY `Post`.`modified` desc LIMIT 20;
#結果省略

20 rows in set (4.07 sec)

mysql>
mysql> #EXPLAIN
mysql> EXPLAIN SELECT SQL_NO_CACHE `Post`.`id`, `Post`.`user_id`, `Post`.`body`, `Post`.`type`, `Post`.`comment_count`, `Post`.`post_like_count`, `Post`.`post_read_count`, `Post`.`public_flg`, `Post`.`important_flg`, `Post`.`goal_id`,`Post`.`created`, `Post`.`modified`, `User`.`id`, `User`.`photo_file_name`, `User`.`timezone`, `User`.`language`, `User`.`auto_language_flg`, `User`.`romanize_flg`, `User`.`first_name`, `User`.`last_name`, `User`.`middle_name`,  (CONCAT(`User`.`first_name`, " ", `User`.`last_name`)) AS `User__username` FROM `myapp`.`posts` AS `Post` LEFT JOIN `myapp`.`users` AS `User` ON (`Post`.`user_id` = `User`.`id` AND `User`.`del_flg` = '0') WHERE `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0' ORDER BY `Post`.`modified` desc LIMIT 20;
+----+-------------+-------+--------+-----------------+---------+---------+--------------------+---------+-----------------------------+
| id | select_type | table | type   | possible_keys   | key     | key_len | ref                | rows    | Extra                       |
+----+-------------+-------+--------+-----------------+---------+---------+--------------------+---------+-----------------------------+
|  1 | SIMPLE      | Post  | ref    | team_id,del_flg | team_id | 8       | const              | 5744038 | Using where; Using filesort |
|  1 | SIMPLE      | User  | eq_ref | PRIMARY,del_flg | PRIMARY | 8       | myapp.Post.user_id |       1 |                             |
+----+-------------+-------+--------+-----------------+---------+---------+--------------------+---------+-----------------------------+
2 rows in set (0.00 sec)

mysql>
mysql>
mysql> #測定クエリその２(過去３ヶ月の投稿データのカウント)
mysql> select SQL_NO_CACHE count(*) from `myapp`.`posts` AS `Post` where `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0';
+----------+
| count(*) |
+----------+
|  3000000 |
+----------+
1 row in set (2.18 sec)

mysql> #EXPLAIN
mysql> EXPLAIN select SQL_NO_CACHE count(*) from `myapp`.`posts` AS `Post` where `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0';
+----+-------------+-------+-------------+-----------------+-----------------+---------+------+---------+------------------------------------------------------------+
| id | select_type | table | type        | possible_keys   | key             | key_len | ref  | rows    | Extra                                                      |
+----+-------------+-------+-------------+-----------------+-----------------+---------+------+---------+------------------------------------------------------------+
|  1 | SIMPLE      | Post  | index_merge | team_id,del_flg | team_id,del_flg | 8,1     | NULL | 2872018 | Using intersect(team_id,del_flg); Using where; Using index |
+----+-------------+-------+-------------+-----------------+-----------------+---------+------+---------+------------------------------------------------------------+
1 row in set (0.01 sec)

mysql>


### modifiedのインデックス追加
mysql> alter table posts add index (modified)
    -> ;
Query OK, 0 rows affected (5 min 50.44 sec)
Records: 0  Duplicates: 0  Warnings: 0

mysql>
mysql> #測定クエリその１(過去３ヶ月の投稿データ取得)
mysql> SELECT SQL_NO_CACHE `Post`.`id`, `Post`.`user_id`, `Post`.`body`, `Post`.`type`, `Post`.`comment_count`, `Post`.`post_like_count`, `Post`.`post_read_count`, `Post`.`public_flg`, `Post`.`important_flg`, `Post`.`goal_id`,`Post`.`created`, `Post`.`modified`, `User`.`id`, `User`.`photo_file_name`, `User`.`timezone`, `User`.`language`, `User`.`auto_language_flg`, `User`.`romanize_flg`, `User`.`first_name`, `User`.`last_name`, `User`.`middle_name`,  (CONCAT(`User`.`first_name`, " ", `User`.`last_name`)) AS `User__username` FROM `myapp`.`posts` AS `Post` LEFT JOIN `myapp`.`users` AS `User` ON (`Post`.`user_id` = `User`.`id` AND `User`.`del_flg` = '0') WHERE `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0' ORDER BY `Post`.`modified` desc LIMIT 20;
#結果省略

20 rows in set (1.21 sec)

mysql>
mysql> #EXPLAIN
mysql> EXPLAIN SELECT SQL_NO_CACHE `Post`.`id`, `Post`.`user_id`, `Post`.`body`, `Post`.`type`, `Post`.`comment_count`, `Post`.`post_like_count`, `Post`.`post_read_count`, `Post`.`public_flg`, `Post`.`important_flg`, `Post`.`goal_id`,`Post`.`created`, `Post`.`modified`, `User`.`id`, `User`.`photo_file_name`, `User`.`timezone`, `User`.`language`, `User`.`auto_language_flg`, `User`.`romanize_flg`, `User`.`first_name`, `User`.`last_name`, `User`.`middle_name`,  (CONCAT(`User`.`first_name`, " ", `User`.`last_name`)) AS `User__username` FROM `myapp`.`posts` AS `Post` LEFT JOIN `myapp`.`users` AS `User` ON (`Post`.`user_id` = `User`.`id` AND `User`.`del_flg` = '0') WHERE `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0' ORDER BY `Post`.`modified` desc LIMIT 20;
+----+-------------+-------+--------+--------------------------+----------+---------+--------------------+----------+-------------+
| id | select_type | table | type   | possible_keys            | key      | key_len | ref                | rows     | Extra       |
+----+-------------+-------+--------+--------------------------+----------+---------+--------------------+----------+-------------+
|  1 | SIMPLE      | Post  | range  | team_id,del_flg,modified | modified | 4       | NULL               | 11418482 | Using where |
|  1 | SIMPLE      | User  | eq_ref | PRIMARY,del_flg          | PRIMARY  | 8       | myapp.Post.user_id |        1 |             |
+----+-------------+-------+--------+--------------------------+----------+---------+--------------------+----------+-------------+
2 rows in set (0.01 sec)

mysql>
mysql> #測定クエリその２(過去３ヶ月の投稿データのカウント)
mysql> select SQL_NO_CACHE count(*) from `myapp`.`posts` AS `Post` where `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0';
+----------+
| count(*) |
+----------+
|  3000000 |
+----------+
1 row in set (2.06 sec)

mysql>
mysql> #EXPLAIN
mysql> EXPLAIN select SQL_NO_CACHE count(*) from `myapp`.`posts` AS `Post` where `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0';
+----+-------------+-------+-------------+--------------------------+-----------------+---------+------+---------+------------------------------------------------------------+
| id | select_type | table | type        | possible_keys            | key             | key_len | ref  | rows    | Extra                                                      |
+----+-------------+-------+-------------+--------------------------+-----------------+---------+------+---------+------------------------------------------------------------+
|  1 | SIMPLE      | Post  | index_merge | team_id,del_flg,modified | team_id,del_flg | 8,1     | NULL | 2872018 | Using intersect(team_id,del_flg); Using where; Using index |
+----+-------------+-------+-------------+--------------------------+-----------------+---------+------+---------+------------------------------------------------------------+
1 row in set (0.00 sec)

mysql>



### インデックスを一切使わないパターン
mysql> #測定クエリその１(過去３ヶ月の投稿データ取得) IGNORE INDEX (team_id,del_flg,modified)
mysql> SELECT SQL_NO_CACHE `Post`.`id`, `Post`.`user_id`, `Post`.`body`, `Post`.`type`, `Post`.`comment_count`, `Post`.`post_like_count`, `Post`.`post_read_count`, `Post`.`public_flg`, `Post`.`important_flg`, `Post`.`goal_id`,`Post`.`created`, `Post`.`modified`, `User`.`id`, `User`.`photo_file_name`, `User`.`timezone`, `User`.`language`, `User`.`auto_language_flg`, `User`.`romanize_flg`, `User`.`first_name`, `User`.`last_name`, `User`.`middle_name`,  (CONCAT(`User`.`first_name`, " ", `User`.`last_name`)) AS `User__username` FROM `myapp`.`posts` AS `Post` IGNORE INDEX (team_id,del_flg,modified) LEFT JOIN `myapp`.`users` AS `User` ON (`Post`.`user_id` = `User`.`id` AND `User`.`del_flg` = '0') WHERE `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0' ORDER BY `Post`.`modified` desc LIMIT 20;
#結果省略

20 rows in set (1 min 10.99 sec)

mysql> EXPLAIN SELECT SQL_NO_CACHE `Post`.`id`, `Post`.`user_id`, `Post`.`body`, `Post`.`type`, `Post`.`comment_count`, `Post`.`post_like_count`, `Post`.`post_read_count`, `Post`.`public_flg`, `Post`.`important_flg`, `Post`.`goal_id`,`Post`.`created`, `Post`.`modified`, `User`.`id`, `User`.`photo_file_name`, `User`.`timezone`, `User`.`language`, `User`.`auto_language_flg`, `User`.`romanize_flg`, `User`.`first_name`, `User`.`last_name`, `User`.`middle_name`,  (CONCAT(`User`.`first_name`, " ", `User`.`last_name`)) AS `User__username` FROM `myapp`.`posts` AS `Post` IGNORE INDEX (team_id,del_flg,modified) LEFT JOIN `myapp`.`users` AS `User` ON (`Post`.`user_id` = `User`.`id` AND `User`.`del_flg` = '0') WHERE `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0' ORDER BY `Post`.`modified` desc LIMIT 20;
+----+-------------+-------+--------+-----------------+---------+---------+--------------------+----------+-----------------------------+
| id | select_type | table | type   | possible_keys   | key     | key_len | ref                | rows     | Extra                       |
+----+-------------+-------+--------+-----------------+---------+---------+--------------------+----------+-----------------------------+
|  1 | SIMPLE      | Post  | ALL    | NULL            | NULL    | NULL    | NULL               | 99833097 | Using where; Using filesort |
|  1 | SIMPLE      | User  | eq_ref | PRIMARY,del_flg | PRIMARY | 8       | myapp.Post.user_id |        1 |                             |
+----+-------------+-------+--------+-----------------+---------+---------+--------------------+----------+-----------------------------+
2 rows in set (0.01 sec)

mysql>

#測定クエリその２(過去３ヶ月の投稿データのカウント)
mysql> select SQL_NO_CACHE count(*) from `myapp`.`posts` AS `Post` IGNORE INDEX (team_id,del_flg,modified) where `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0';
+----------+
| count(*) |
+----------+
|  3000000 |
+----------+
1 row in set (1 min 5.84 sec)

mysql>
mysql> EXPLAIN  select SQL_NO_CACHE count(*) from `myapp`.`posts` AS `Post` IGNORE INDEX (team_id,del_flg,modified) where `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0';
+----+-------------+-------+------+---------------+------+---------+------+----------+-------------+
| id | select_type | table | type | possible_keys | key  | key_len | ref  | rows     | Extra       |
+----+-------------+-------+------+---------------+------+---------+------+----------+-------------+
|  1 | SIMPLE      | Post  | ALL  | NULL          | NULL | NULL    | NULL | 99833097 | Using where |
+----+-------------+-------+------+---------------+------+---------+------+----------+-------------+
1 row in set (0.01 sec)

mysql>

### インデックスをmodifiedのみ使うパターン
mysql> #測定クエリその１(過去３ヶ月の投稿データ取得) IGNORE INDEX (team_id,del_flg)
mysql> SELECT SQL_NO_CACHE `Post`.`id`, `Post`.`user_id`, `Post`.`body`, `Post`.`type`, `Post`.`comment_count`, `Post`.`post_like_count`, `Post`.`post_read_count`, `Post`.`public_flg`, `Post`.`important_flg`, `Post`.`goal_id`,`Post`.`created`, `Post`.`modified`, `User`.`id`, `User`.`photo_file_name`, `User`.`timezone`, `User`.`language`, `User`.`auto_language_flg`, `User`.`romanize_flg`, `User`.`first_name`, `User`.`last_name`, `User`.`middle_name`,  (CONCAT(`User`.`first_name`, " ", `User`.`last_name`)) AS `User__username` FROM `myapp`.`posts` AS `Post` IGNORE INDEX (team_id,del_flg) LEFT JOIN `myapp`.`users` AS `User` ON (`Post`.`user_id` = `User`.`id` AND `User`.`del_flg` = '0') WHERE `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0' ORDER BY `Post`.`modified` desc LIMIT 20;
#結果省略

20 rows in set (1.19 sec)

mysql> explain SELECT SQL_NO_CACHE `Post`.`id`, `Post`.`user_id`, `Post`.`body`, `Post`.`type`, `Post`.`comment_count`, `Post`.`post_like_count`, `Post`.`post_read_count`, `Post`.`public_flg`, `Post`.`important_flg`, `Post`.`goal_id`,`Post`.`created`, `Post`.`modified`, `User`.`id`, `User`.`photo_file_name`, `User`.`timezone`, `User`.`language`, `User`.`auto_language_flg`, `User`.`romanize_flg`, `User`.`first_name`, `User`.`last_name`, `User`.`middle_name`,  (CONCAT(`User`.`first_name`, " ", `User`.`last_name`)) AS `User__username` FROM `myapp`.`posts` AS `Post` IGNORE INDEX (team_id,del_flg) LEFT JOIN `myapp`.`users` AS `User` ON (`Post`.`user_id` = `User`.`id` AND `User`.`del_flg` = '0') WHERE `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0' ORDER BY `Post`.`modified` desc LIMIT 20;
+----+-------------+-------+--------+-----------------+----------+---------+--------------------+----------+-------------+
| id | select_type | table | type   | possible_keys   | key      | key_len | ref                | rows     | Extra       |
+----+-------------+-------+--------+-----------------+----------+---------+--------------------+----------+-------------+
|  1 | SIMPLE      | Post  | range  | modified        | modified | 4       | NULL               | 11418482 | Using where |
|  1 | SIMPLE      | User  | eq_ref | PRIMARY,del_flg | PRIMARY  | 8       | myapp.Post.user_id |        1 |             |
+----+-------------+-------+--------+-----------------+----------+---------+--------------------+----------+-------------+
2 rows in set (0.01 sec)

mysql>

#測定クエリその２(過去３ヶ月の投稿データのカウント)
mysql> select SQL_NO_CACHE count(*) from `myapp`.`posts` AS `Post` IGNORE INDEX (team_id,del_flg) where `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0';
+----------+
| count(*) |
+----------+
|  3000000 |
+----------+
1 row in set (16.75 sec)

mysql> explain select SQL_NO_CACHE count(*) from `myapp`.`posts` AS `Post` IGNORE INDEX (team_id,del_flg) where `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0';
+----+-------------+-------+-------+---------------+----------+---------+------+----------+-------------+
| id | select_type | table | type  | possible_keys | key      | key_len | ref  | rows     | Extra       |
+----+-------------+-------+-------+---------------+----------+---------+------+----------+-------------+
|  1 | SIMPLE      | Post  | range | modified      | modified | 4       | NULL | 11418482 | Using where |
+----+-------------+-------+-------+---------------+----------+---------+------+----------+-------------+
1 row in set (0.00 sec)

mysql>



#####パーティショニング

#パーティショニング作業
mysql> ALTER TABLE posts PARTITION BY RANGE (modified)
    -> (
    -> PARTITION p201401 VALUES LESS THAN (UNIX_TIMESTAMP('2014-02-01')) ENGINE=InnoDB,
    -> PARTITION p201402 VALUES LESS THAN (UNIX_TIMESTAMP('2014-03-01')) ENGINE=InnoDB,
    -> PARTITION p201403 VALUES LESS THAN (UNIX_TIMESTAMP('2014-04-01')) ENGINE=InnoDB,
    -> PARTITION p201404 VALUES LESS THAN (UNIX_TIMESTAMP('2014-05-01')) ENGINE=InnoDB,
    -> PARTITION p201405 VALUES LESS THAN (UNIX_TIMESTAMP('2014-06-01')) ENGINE=InnoDB,
    -> PARTITION p201406 VALUES LESS THAN (UNIX_TIMESTAMP('2014-07-01')) ENGINE=InnoDB,
    -> PARTITION p201407 VALUES LESS THAN (UNIX_TIMESTAMP('2014-08-01')) ENGINE=InnoDB,
    -> PARTITION p201408 VALUES LESS THAN (UNIX_TIMESTAMP('2014-09-01')) ENGINE=InnoDB,
    -> PARTITION pmax VALUES LESS THAN MAXVALUE ENGINE=InnoDB
    -> );
Query OK, 100000010 rows affected (35 min 56.96 sec)
Records: 100000010  Duplicates: 0  Warnings: 0

#パーティション設定確認
mysql> select TABLE_SCHEMA,TABLE_NAME,PARTITION_NAME,PARTITION_ORDINAL_POSITION,TABLE_ROWS from INFORMATION_SCHEMA.PARTITIONS WHERE TABLE_NAME='posts';
+--------------+------------+----------------+----------------------------+------------+
| TABLE_SCHEMA | TABLE_NAME | PARTITION_NAME | PARTITION_ORDINAL_POSITION | TABLE_ROWS |
+--------------+------------+----------------+----------------------------+------------+
| myapp        | posts      | p201401        |                          1 |   91118656 |
| myapp        | posts      | p201402        |                          2 |    2276980 |
| myapp        | posts      | p201403        |                          3 |     599121 |
| myapp        | posts      | p201404        |                          4 |          0 |
| myapp        | posts      | p201405        |                          5 |    2001602 |
| myapp        | posts      | p201406        |                          6 |    2000008 |
| myapp        | posts      | p201407        |                          7 |    2001602 |
| myapp        | posts      | p201408        |                          8 |          0 |
| myapp        | posts      | pmax           |                          9 |          0 |
+--------------+------------+----------------+----------------------------+------------+
9 rows in set (0.21 sec)

mysql>


#測定クエリその２(過去３ヶ月の投稿データのカウント)index利用しないパターン
mysql> SELECT SQL_NO_CACHE `Post`.`id`, `Post`.`user_id`, `Post`.`body`, `Post`.`type`, `Post`.`comment_count`, `Post`.`post_like_count`, `Post`.`post_read_count`, `Post`.`public_flg`, `Post`.`important_flg`, `Post`.`goal_id`,`Post`.`created`, `Post`.`modified`, `User`.`id`, `User`.`photo_file_name`, `User`.`timezone`, `User`.`language`, `User`.`auto_language_flg`, `User`.`romanize_flg`, `User`.`first_name`, `User`.`last_name`, `User`.`middle_name`,  (CONCAT(`User`.`first_name`, " ", `User`.`last_name`)) AS `User__username` FROM `myapp`.`posts` AS `Post` IGNORE INDEX (team_id,del_flg,modified) LEFT JOIN `myapp`.`users` AS `User` ON (`Post`.`user_id` = `User`.`id` AND `User`.`del_flg` = '0') WHERE `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0' ORDER BY `Post`.`modified` desc LIMIT 20;

#結果省略

20 rows in set (3.70 sec)


mysql> explain SELECT SQL_NO_CACHE `Post`.`id`, `Post`.`user_id`, `Post`.`body`, `Post`.`type`, `Post`.`comment_count`, `Post`.`post_like_count`, `Post`.`post_read_count`, `Post`.`public_flg`, `Post`.`important_flg`, `Post`.`goal_id`,`Post`.`created`, `Post`.`modified`, `User`.`id`, `User`.`photo_file_name`, `User`.`timezone`, `User`.`language`, `User`.`auto_language_flg`, `User`.`romanize_flg`, `User`.`first_name`, `User`.`last_name`, `User`.`middle_name`,  (CONCAT(`User`.`first_name`, " ", `User`.`last_name`)) AS `User__username` FROM `myapp`.`posts` AS `Post` IGNORE INDEX (team_id,del_flg,modified) LEFT JOIN `myapp`.`users` AS `User` ON (`Post`.`user_id` = `User`.`id` AND `User`.`del_flg` = '0') WHERE `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0' ORDER BY `Post`.`modified` desc LIMIT 20;
+----+-------------+-------+--------+-----------------+---------+---------+--------------------+---------+-----------------------------+
| id | select_type | table | type   | possible_keys   | key     | key_len | ref                | rows    | Extra                       |
+----+-------------+-------+--------+-----------------+---------+---------+--------------------+---------+-----------------------------+
|  1 | SIMPLE      | Post  | ALL    | NULL            | NULL    | NULL    | NULL               | 6001619 | Using where; Using filesort |
|  1 | SIMPLE      | User  | eq_ref | PRIMARY,del_flg | PRIMARY | 8       | myapp.Post.user_id |       1 |                             |
+----+-------------+-------+--------+-----------------+---------+---------+--------------------+---------+-----------------------------+
2 rows in set (0.01 sec)

mysql>
mysql> explain partitions SELECT SQL_NO_CACHE `Post`.`id`, `Post`.`user_id`, `Post`.`body`, `Post`.`type`, `Post`.`comment_count`, `Post`.`post_like_count`, `Post`.`post_read_count`, `Post`.`public_flg`, `Post`.`important_flg`, `Post`.`goal_id`,`Post`.`created`, `Post`.`modified`, `User`.`id`, `User`.`photo_file_name`, `User`.`timezone`, `User`.`language`, `User`.`auto_language_flg`, `User`.`romanize_flg`, `User`.`first_name`, `User`.`last_name`, `User`.`middle_name`,  (CONCAT(`User`.`first_name`, " ", `User`.`last_name`)) AS `User__username` FROM `myapp`.`posts` AS `Post` IGNORE INDEX (team_id,del_flg,modified) LEFT JOIN `myapp`.`users` AS `User` ON (`Post`.`user_id` = `User`.`id` AND `User`.`del_flg` = '0') WHERE `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0' ORDER BY `Post`.`modified` desc LIMIT 20;
+----+-------------+-------+---------------------------------+--------+-----------------+---------+---------+--------------------+---------+-----------------------------+
| id | select_type | table | partitions                      | type   | possible_keys   | key     | key_len | ref                | rows    | Extra                       |
+----+-------------+-------+---------------------------------+--------+-----------------+---------+---------+--------------------+---------+-----------------------------+
|  1 | SIMPLE      | Post  | p201404,p201405,p201406,p201407 | ALL    | NULL            | NULL    | NULL    | NULL               | 6003213 | Using where; Using filesort |
|  1 | SIMPLE      | User  | NULL                            | eq_ref | PRIMARY,del_flg | PRIMARY | 8       | myapp.Post.user_id |       1 |                             |
+----+-------------+-------+---------------------------------+--------+-----------------+---------+---------+--------------------+---------+-----------------------------+
2 rows in set (0.00 sec)

mysql>

mysql> select SQL_NO_CACHE count(*) from `myapp`.`posts` AS `Post` IGNORE INDEX (team_id,del_flg,modified) where `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0';
+----------+
| count(*) |
+----------+
|  3000000 |
+----------+
1 row in set (2.35 sec)

mysql> explain select SQL_NO_CACHE count(*) from `myapp`.`posts` AS `Post` IGNORE INDEX (team_id,del_flg,modified) where `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0';
+----+-------------+-------+------+---------------+------+---------+------+---------+-------------+
| id | select_type | table | type | possible_keys | key  | key_len | ref  | rows    | Extra       |
+----+-------------+-------+------+---------------+------+---------+------+---------+-------------+
|  1 | SIMPLE      | Post  | ALL  | NULL          | NULL | NULL    | NULL | 6001619 | Using where |
+----+-------------+-------+------+---------------+------+---------+------+---------+-------------+
1 row in set (0.00 sec)

mysql>
mysql> explain partitions select SQL_NO_CACHE count(*) from `myapp`.`posts` AS `Post` IGNORE INDEX (team_id,del_flg,modified) where `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0';
+----+-------------+-------+---------------------------------+------+---------------+------+---------+------+---------+-------------+
| id | select_type | table | partitions                      | type | possible_keys | key  | key_len | ref  | rows    | Extra       |
+----+-------------+-------+---------------------------------+------+---------------+------+---------+------+---------+-------------+
|  1 | SIMPLE      | Post  | p201404,p201405,p201406,p201407 | ALL  | NULL          | NULL | NULL    | NULL | 6003213 | Using where |
+----+-------------+-------+---------------------------------+------+---------------+------+---------+------+---------+-------------+
1 row in set (0.00 sec)

mysql>



### インデックスをmodifiedのみ使うパターン
mysql> #測定クエリその１(過去３ヶ月の投稿データ取得) IGNORE INDEX (team_id,del_flg)
mysql> SELECT SQL_NO_CACHE `Post`.`id`, `Post`.`user_id`, `Post`.`body`, `Post`.`type`, `Post`.`comment_count`, `Post`.`post_like_count`, `Post`.`post_read_count`, `Post`.`public_flg`, `Post`.`important_flg`, `Post`.`goal_id`,`Post`.`created`, `Post`.`modified`, `User`.`id`, `User`.`photo_file_name`, `User`.`timezone`, `User`.`language`, `User`.`auto_language_flg`, `User`.`romanize_flg`, `User`.`first_name`, `User`.`last_name`, `User`.`middle_name`,  (CONCAT(`User`.`first_name`, " ", `User`.`last_name`)) AS `User__username` FROM `myapp`.`posts` AS `Post` IGNORE INDEX (team_id,del_flg) LEFT JOIN `myapp`.`users` AS `User` ON (`Post`.`user_id` = `User`.`id` AND `User`.`del_flg` = '0') WHERE `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0' ORDER BY `Post`.`modified` desc LIMIT 20;
#結果省略
20 rows in set (1.27 sec)

mysql> explain SELECT SQL_NO_CACHE `Post`.`id`, `Post`.`user_id`, `Post`.`body`, `Post`.`type`, `Post`.`comment_count`, `Post`.`post_like_count`, `Post`.`post_read_count`, `Post`.`public_flg`, `Post`.`important_flg`, `Post`.`goal_id`,`Post`.`created`, `Post`.`modified`, `User`.`id`, `User`.`photo_file_name`, `User`.`timezone`, `User`.`language`, `User`.`auto_language_flg`, `User`.`romanize_flg`, `User`.`first_name`, `User`.`last_name`, `User`.`middle_name`,  (CONCAT(`User`.`first_name`, " ", `User`.`last_name`)) AS `User__username` FROM `myapp`.`posts` AS `Post` IGNORE INDEX (team_id,del_flg) LEFT JOIN `myapp`.`users` AS `User` ON (`Post`.`user_id` = `User`.`id` AND `User`.`del_flg` = '0') WHERE `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0' ORDER BY `Post`.`modified` desc LIMIT 20;
+----+-------------+-------+--------+-----------------+----------+---------+--------------------+---------+-------------+
| id | select_type | table | type   | possible_keys   | key      | key_len | ref                | rows    | Extra       |
+----+-------------+-------+--------+-----------------+----------+---------+--------------------+---------+-------------+
|  1 | SIMPLE      | Post  | range  | modified        | modified | 4       | NULL               | 3001607 | Using where |
|  1 | SIMPLE      | User  | eq_ref | PRIMARY,del_flg | PRIMARY  | 8       | myapp.Post.user_id |       1 |             |
+----+-------------+-------+--------+-----------------+----------+---------+--------------------+---------+-------------+
2 rows in set (0.00 sec)

mysql> explain partitions SELECT SQL_NO_CACHE `Post`.`id`, `Post`.`user_id`, `Post`.`body`, `Post`.`type`, `Post`.`comment_count`, `Post`.`post_like_count`, `Post`.`post_read_count`, `Post`.`public_flg`, `Post`.`important_flg`, `Post`.`goal_id`,`Post`.`created`, `Post`.`modified`, `User`.`id`, `User`.`photo_file_name`, `User`.`timezone`, `User`.`language`, `User`.`auto_language_flg`, `User`.`romanize_flg`, `User`.`first_name`, `User`.`last_name`, `User`.`middle_name`,  (CONCAT(`User`.`first_name`, " ", `User`.`last_name`)) AS `User__username` FROM `myapp`.`posts` AS `Post` IGNORE INDEX (team_id,del_flg) LEFT JOIN `myapp`.`users` AS `User` ON (`Post`.`user_id` = `User`.`id` AND `User`.`del_flg` = '0') WHERE `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0' ORDER BY `Post`.`modified` desc LIMIT 20;
+----+-------------+-------+---------------------------------+--------+-----------------+----------+---------+--------------------+---------+-------------+
| id | select_type | table | partitions                      | type   | possible_keys   | key      | key_len | ref                | rows    | Extra       |
+----+-------------+-------+---------------------------------+--------+-----------------+----------+---------+--------------------+---------+-------------+
|  1 | SIMPLE      | Post  | p201404,p201405,p201406,p201407 | range  | modified        | modified | 4       | NULL               | 3001607 | Using where |
|  1 | SIMPLE      | User  | NULL                            | eq_ref | PRIMARY,del_flg | PRIMARY  | 8       | myapp.Post.user_id |       1 |             |
+----+-------------+-------+---------------------------------+--------+-----------------+----------+---------+--------------------+---------+-------------+
2 rows in set (0.00 sec)

mysql>

#測定クエリその２(過去３ヶ月の投稿データのカウント)
mysql> select SQL_NO_CACHE count(*) from `myapp`.`posts` AS `Post` IGNORE INDEX (team_id,del_flg) where `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0';
+----------+
| count(*) |
+----------+
|  3000000 |
+----------+
1 row in set (2.48 sec)

mysql>

mysql> explain select SQL_NO_CACHE count(*) from `myapp`.`posts` AS `Post` IGNORE INDEX (team_id,del_flg) where `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0';
+----+-------------+-------+------+---------------+------+---------+------+---------+-------------+
| id | select_type | table | type | possible_keys | key  | key_len | ref  | rows    | Extra       |
+----+-------------+-------+------+---------------+------+---------+------+---------+-------------+
|  1 | SIMPLE      | Post  | ALL  | modified      | NULL | NULL    | NULL | 6003213 | Using where |
+----+-------------+-------+------+---------------+------+---------+------+---------+-------------+
1 row in set (0.00 sec)

mysql> explain partitions select SQL_NO_CACHE count(*) from `myapp`.`posts` AS `Post` IGNORE INDEX (team_id,del_flg) where `Post`.`modified` BETWEEN 1397524918 AND 1405387316 AND `Post`.`team_id` = 1 AND `Post`.`del_flg` = '0';
+----+-------------+-------+---------------------------------+------+---------------+------+---------+------+---------+-------------+
| id | select_type | table | partitions                      | type | possible_keys | key  | key_len | ref  | rows    | Extra       |
+----+-------------+-------+---------------------------------+------+---------------+------+---------+------+---------+-------------+
|  1 | SIMPLE      | Post  | p201404,p201405,p201406,p201407 | ALL  | modified      | NULL | NULL    | NULL | 6003213 | Using where |
+----+-------------+-------+---------------------------------+------+---------------+------+---------+------+---------+-------------+
1 row in set (0.00 sec)

mysql>


```
