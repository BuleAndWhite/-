<?php


namespace Api\Model;

use Think\Model;
class SpecModel extends Model
{
    /***
     * Notes:职位筛选检索
     * param $arr 筛选的所有值
     * User: Belise
     * DateTime: 2020-06-30 12:50
     * Return :array()职位列表
     */
    public function recruitmentFilter($arr, $str)
    {
        $sql = "SELECT
	e.id AS cid,
	d.enterprise_id,
	d.bid AS id,
	e.NAME,
	d.position,
	d.time,
	d.avatar ,
	d.is_anxious ,
	d.address_x,
	d.address_y
FROM
	(
	SELECT
		b.id AS bid,
		b.enterprise_id,
		b.position,
		b.time,
		b.is_anxious,
		b.content,
		b.address_x,
		b.address_y,
		b.ishot,
		c.NAME AS cname,
		c.avatar 
	FROM
		(
		SELECT
			* 
		FROM
			( SELECT *, group_concat( spec_info_id ORDER BY spec_info_id ) AS space_list FROM az_spec_label WHERE spec_info_id IN ".$arr." GROUP BY object_id ) AS a 
		WHERE
			a.space_list LIKE  ". $str ."
		) AS a
		LEFT JOIN az_recruitment AS b ON a.object_id = b.id
		INNER JOIN az_user AS c ON b.uid = c.id 
	) AS d
	LEFT JOIN az_user_enterpise AS e ON d.enterprise_id = e.id;";
        return $sql;
    }
}