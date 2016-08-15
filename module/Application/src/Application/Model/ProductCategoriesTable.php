<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Delete;
use Zend\Db\Sql\Update;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Paginator\Paginator;
use Zend\Db\ResultSet\ResultSet;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Sql\Where;
use Zend\Db\Adapter\Driver\Pdo\Result;

class ProductCategoriesTable extends BasicTableAdapter {

    protected $sTable = 'ProductCategories';

    public function updateCatagoriesTable()
    {
        //echo "test"; die;
        $dbAdpr = $this->getDBAdapter();
        $dbAdpr->query( "TRUNCATE ProductCategories_for_processing;" )->execute();
      
        # CLOTHING -> OUTERWEAR	 -> DENIM
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select productUID, '13', 1 from (
        	SELECT distinct p1.productUID
        	FROM ProductAttributes  p1
        	INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value in ('428','434','417','438','425')
        	INNER JOIN ProductAttributes  p3 on p2.productUID = p3.productUID and p3.TYPE = 'Fabric' AND p3.value = 'Denim'
        	WHERE p1.TYPE = 'categories' AND p1.value = '6'
        	UNION 
        	select distinct productUID from ProductAttributes where TYPE = 'categories' AND value = '428' AND productUID NOT IN (
        		SELECT distinct p1.productUID 
        		FROM ProductAttributes  p1
        		INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value IN ('428','434','417','438','425','440','439')
        		INNER JOIN ProductAttributes  p3 on p2.productUID = p3.productUID and p3.TYPE = 'Fabric' AND p3.value IN ('Leather','Suede','Leatherette','Faux Leather','Pleather','Faux Suede')
        		WHERE p1.TYPE = 'categories' AND p1.value = '6'
        	)
        ) t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";        
        $dbAdpr->query($sQuery)->execute();  

        # CLOTHING -> OUTERWEAR	 -> LEATHER & SUEDE
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select productUID, '15', 1 from (
        	SELECT distinct p1.productUID 
        	FROM ProductAttributes  p1
        	INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value IN ('428','434','417','438','425','440','439')
        	INNER JOIN ProductAttributes  p3 on p2.productUID = p3.productUID and p3.TYPE = 'Fabric' AND p3.value IN ('Leather','Suede','Leatherette','Faux Leather','Pleather','Faux Suede')
        	WHERE p1.TYPE = 'categories' AND p1.value = '6'	
        ) t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();        
        
        # CLOTHING -> OUTERWEAR	 -> OVERCOATS & TRENCH COATS
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select productUID, '16', 1 from (
        	SELECT distinct p1.productUID 
        	FROM ProductAttributes  p1
        	INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value IN ('415','420','424','446','419','445')
        	WHERE p1.TYPE = 'categories' AND p1.value = '6'	
        ) t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();
        
        # CLOTHING -> OUTERWEAR	 -> JACKETS & COATS
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select productUID, '14', 1 from (
        	SELECT distinct productUID FROM ProductAttributes  WHERE TYPE = 'categories' AND value = '6' AND productUID NOT IN (
        		SELECT distinct p1.productUID 
        		FROM ProductAttributes  p1
        		INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value in ('428','434','417','438','425')
        		INNER JOIN ProductAttributes  p3 on p2.productUID = p3.productUID and p3.TYPE = 'Fabric' AND p3.value = 'Denim'
        		WHERE p1.TYPE = 'categories' AND p1.value = '6'
        		UNION 
        		select distinct productUID from ProductAttributes where TYPE = 'categories' AND value = '428' AND productUID NOT IN (
        			SELECT distinct p1.productUID 
        			FROM ProductAttributes  p1
        			INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value IN ('428','434','417','438','425','440','439')
        			INNER JOIN ProductAttributes  p3 on p2.productUID = p3.productUID and p3.TYPE = 'Fabric' AND p3.value IN ('Leather','Suede','Leatherette','Faux Leather','Pleather','Faux Suede')
        			WHERE p1.TYPE = 'categories' AND p1.value = '6'
        		)
        		UNION
        		SELECT distinct p1.productUID 
        		FROM ProductAttributes  p1
        		INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value IN ('428','434','417','438','425','440','439')
        		INNER JOIN ProductAttributes  p3 on p2.productUID = p3.productUID and p3.TYPE = 'Fabric' AND p3.value IN ('Leather','Suede','Leatherette','Faux Leather','Pleather','Faux Suede')
        		WHERE p1.TYPE = 'categories' AND p1.value = '6'
        		UNION
        		SELECT distinct p1.productUID 
        		FROM ProductAttributes  p1
        		INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value IN ('415','420','424','446','419','445')
        		WHERE p1.TYPE = 'categories' AND p1.value = '6'
        		UNION
        		SELECT distinct p1.productUID 
        		FROM ProductAttributes  p1
        		INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value IN ('432','430','436','442','444','416','1890','427','429','431','1140','1142','1149','7997',
        		'433','435','437','441','443','1145','1147','1153','7997','7999','7988')
        		INNER JOIN ProductAttributes  p3 on p2.productUID = p3.productUID and p3.TYPE = 'Feature' AND p3.value IN ('ACTIVEWEAR')
        		WHERE p1.TYPE = 'categories' AND p1.value = '6' 
        	)	
        ) t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();
        
        # CLOTHING -> TOPS	 -> DRESS SHIRT
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select productUID, '17', 1 from (
        	SELECT distinct p1.productUID 
        	FROM ProductAttributes  p1
        	INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value IN ('1123','1125')
        	WHERE p1.TYPE = 'categories' AND p1.value = '7'	
        ) t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();
        
        # CLOTHING -> TOPS	 -> T-SHIRT
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select productUID, '18', 1 from (
        	SELECT distinct p1.productUID 
        	FROM ProductAttributes  p1
        	INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value IN ('1127','1129','1133','7992')
        	WHERE p1.TYPE = 'categories' AND p1.value = '7'	
        ) t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();
        
        # CLOTHING -> TOPS	 -> LONGSLEEVE
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select productUID, '19', 1 from (
        	SELECT distinct p1.productUID 
        	FROM ProductAttributes  p1
        	INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value IN ('1128','1130','1135','1197')
        	WHERE p1.TYPE = 'categories' AND p1.value = '7'	
        ) t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();
        
        # CLOTHING -> TOPS	 -> SLEEVELESS SHIRT
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select productUID, '20', 1 from (
        	SELECT distinct p1.productUID 
        	FROM ProductAttributes  p1
        	INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value IN ('1132','1146','1152')
        	WHERE p1.TYPE = 'categories' AND p1.value = '7'	
        ) t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();
        
        # CLOTHING -> TOPS -> SWEATER
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select productUID, '21', 1 from (
        	SELECT distinct p1.productUID 
        	FROM ProductAttributes  p1
        	INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value IN ('1145','1139','1145','7993','7994','7995','7996')
        	WHERE p1.TYPE = 'categories' AND p1.value = '7'	
        ) t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();
        
        # CLOTHING -> TOPS	 -> CARDIGANS & ZIP UPS
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select productUID, '22', 1 from (
        	SELECT distinct p1.productUID 
        	FROM ProductAttributes  p1
        	INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value in ('431','1140','1142','1149','7997','7998','7999','8000')
        	WHERE p1.TYPE = 'categories' AND p1.value = '7'
        	UNION
        	SELECT distinct p1.productUID 
        	FROM ProductAttributes  p1
        	INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value in ('1147','427','1154','1139','1141','1145','7993','7994','7995','7996')
        	INNER JOIN ProductAttributes  p3 on p2.productUID = p3.productUID and p3.TYPE = 'Fabric' AND p3.value IN ('Half Zipper','Full Zipper')
        	WHERE p1.TYPE = 'categories' AND p1.value = '7'	
        ) t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();
        
        # CLOTHING -> TOPS -> SWEATSHIRT & HOODIES
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select productUID, '23', 1 from (
        	SELECT distinct p1.productUID 
        	FROM ProductAttributes  p1
        	INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value IN ('1136','1143','1148','1154')
        	WHERE p1.TYPE = 'categories' AND p1.value = '7'	
        ) t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();
        
        # CLOTHING -> SUITS & TUXEDOS
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select productUID, '7', 1 from (
        	SELECT distinct p1.productUID 
        	FROM ProductAttributes  p1
        	INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value IN ('15','36','1886','1887','2648','1888')
        	WHERE p1.TYPE = 'categories' AND p1.value = '36'	
        ) t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();
        
        # CLOTHING -> SPORT JACKETS
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select productUID, '8', 1 from (
        	SELECT distinct p1.productUID 
        	FROM ProductAttributes  p1
        	INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value IN ('1889','416','1890','1891')
        	WHERE p1.TYPE = 'categories' AND p1.value = '36'	
        ) t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();
        
        # CLOTHING -> BOTTOMS -> JEANS
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select productUID, '24', 1 from (
        	SELECT distinct p1.productUID 
        	FROM ProductAttributes  p1
        	INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value IN ('2605','2635','2661')
        	WHERE p1.TYPE = 'categories' AND p1.value = '37'
        	UNION
        	SELECT distinct p1.productUID 
        	FROM ProductAttributes  p1
        	INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'Fabric' AND p2.value IN ('Denim')
        	WHERE p1.TYPE = 'categories' AND p1.value = '37'	
        ) t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();
        
        # CLOTHING -> BOTTOMS -> DRESS PANTS
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select productUID, '25', 1 from (
        	SELECT distinct p1.productUID 
        	FROM ProductAttributes  p1
        	INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value IN ('2606','2644','2671','2673','2613','2660')
        	WHERE p1.TYPE = 'categories' AND p1.value = '37'	
        ) t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();
        
        # CLOTHING -> BOTTOMS -> KHAKIS & CHINOS
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select productUID, '26', 1 from (
        	SELECT distinct productUID FROM ProductAttributes  WHERE TYPE = 'categories' AND value = '37' AND productUID NOT IN (
        		SELECT distinct p1.productUID 
        		FROM ProductAttributes  p1
        		INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value IN ('2605','2635','2661')
        		WHERE p1.TYPE = 'categories' AND p1.value = '37'
        		UNION
        		SELECT distinct p1.productUID 
        		FROM ProductAttributes  p1
        		INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'Fabric' AND p2.value IN ('Denim')
        		WHERE p1.TYPE = 'categories' AND p1.value = '37'
        		UNION
        		SELECT distinct p1.productUID 
        		FROM ProductAttributes  p1
        		INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value IN ('2606','2644','2671','2673','2613','2660')
        		WHERE p1.TYPE = 'categories' AND p1.value = '37'
        	)	
        ) t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();
        
        # CLOTHING -> BOTTOMS -> SHORTS
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        	select productUID, '27', 1 from (
        	SELECT distinct p1.productUID 
        	FROM ProductAttributes  p1
        	INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value IN ('3371','3375')
        	WHERE p1.TYPE = 'categories' AND p1.value = '54'	
        ) t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();
        
        # CLOTHING -> UNDERWEAR & SOCKS -> UNDERWEAR
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select productUID, '28', 1 from (
        	SELECT distinct p1.productUID 
        	FROM ProductAttributes  p1
        	WHERE p1.TYPE = 'categories' AND p1.value IN ('69')	
        ) t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();
        
        # CLOTHING -> UNDERWEAR & SOCKS -> SOCKS
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select productUID, '29', 1 from (
        	SELECT distinct p1.productUID 
        	FROM ProductAttributes  p1
        	WHERE p1.TYPE = 'categories' AND p1.value IN ('70')
        ) t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();
        
        # CLOTHING -> ACTIVEWEAR -> JACKETS
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select productUID, '30', 1 from (
        	SELECT distinct p1.productUID 
        	FROM ProductAttributes  p1
        	INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value IN ('432','430','436','442','444','416','1890','427','429','431','1140','1142','1149','7997',
        	'433','435','437','441','443','1145','1147','1153','7997','7999','7988')
        	INNER JOIN ProductAttributes  p3 on p2.productUID = p3.productUID and p3.TYPE = 'Feature' AND p3.value IN ('ACTIVEWEAR')
        	WHERE p1.TYPE = 'categories' AND p1.value = '6'	
        ) t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();
        
        # CLOTHING -> ACTIVEWEAR -> SHIRTS
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select productUID, '31', 1 from (
        	SELECT distinct p1.productUID 
        	FROM ProductAttributes  p1
        	INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value IN ('1129','1130','1131','7992','1132','1134','1136','1143','1145','1147','1153','7997','1128','1130',
        	'1133','1135','7992','1139','1141','1145','7993','7994','7995','7996','1144','1151','1146','1152','1148')
        	INNER JOIN ProductAttributes  p3 on p2.productUID = p3.productUID and p3.TYPE = 'Feature' AND p3.value IN ('ACTIVEWEAR')
        	WHERE p1.TYPE = 'categories' AND p1.value = '7'	
        ) t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();
        
        # CLOTHING -> ACTIVEWEAR -> SHORTS
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select productUID, '32', 1 from (
        	SELECT distinct p1.productUID 
        	FROM ProductAttributes  p1
        	INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value IN ('3376','3379','3380','3381')
        	INNER JOIN ProductAttributes  p3 on p2.productUID = p3.productUID and p3.TYPE = 'Feature' AND p3.value IN ('ACTIVEWEAR')
        	WHERE p1.TYPE = 'categories' AND p1.value = '54'	
        ) t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();
        
        # CLOTHING -> ACTIVEWEAR -> SWEAT PANTS
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select productUID, '33', 1 from (
        	SELECT distinct p1.productUID 
        	FROM ProductAttributes  p1
        	INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value IN ('2663','2666','8005','2624','2674','2616')
        	INNER JOIN ProductAttributes  p3 on p2.productUID = p3.productUID and p3.TYPE = 'Feature' AND p3.value IN ('ACTIVEWEAR')
        	WHERE p1.TYPE = 'categories' AND p1.value = '37'	
        ) t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();
        
        # CLOTHING -> SWIMWEAR
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select productUID, '12', 1 from (
        	SELECT distinct p1.productUID 
        	FROM ProductAttributes  p1
        	INNER JOIN ProductAttributes  p2 on p1.productUID = p2.productUID and p2.TYPE = 'categories' AND p2.value IN ('3382','3372')
        	WHERE p1.TYPE = 'categories' AND p1.value = '54'	
        ) t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();
        
        # FOOTWEAR -> Sneakers
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select * from (SELECT productUID, 34, 1 FROM ProductAttributes  p1 WHERE p1.TYPE = 'categories' AND p1.value = '41') t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();
        
        # FOOTWEAR -> Sandals
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select * from (SELECT productUID, 35, 1 FROM ProductAttributes  p1 WHERE p1.TYPE = 'categories' AND p1.value = '42') t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();
        
        # FOOTWEAR -> Dress
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select * from (SELECT productUID, 36, 1 FROM ProductAttributes  p1 WHERE p1.TYPE = 'categories' AND p1.value = '46') t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();
        
        # FOOTWEAR -> Casual
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select * from (SELECT productUID, 37, 1 FROM ProductAttributes  p1 WHERE p1.TYPE = 'categories' AND p1.value = '47') t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();
        
        # FOOTWEAR ->  Boots
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select * from (SELECT productUID, 38, 1 FROM ProductAttributes  p1 WHERE p1.TYPE = 'categories' AND p1.value = '48') t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();
        
        # ACCESSORIES -> all
        $sQuery = "INSERT INTO `ProductCategories_for_processing`(`productUID`,`value`,`attribute_count`)
        select * from (
        	SELECT productUID, 
        	 CASE
        			WHEN p1.value = '3'  THEN '39'
        			WHEN p1.value = '43'  THEN '40'
        			WHEN p1.value = '44'  THEN '41'
        			WHEN p1.value = '62'  THEN '42'
        			WHEN p1.value = '65'  THEN '43'
        			WHEN p1.value = '66'  THEN '44'
        			WHEN p1.value = '67'  THEN '45'
        			WHEN p1.value = '7959'  THEN '46'
        			WHEN p1.value = '7960'  THEN '47'
        			WHEN p1.value = '7961'  THEN '48'
        			WHEN p1.value = '7962'  THEN '49'
        			WHEN p1.value = '7963'  THEN '50'
        			WHEN p1.value = '7964'  THEN '51'
        			WHEN p1.value = '7965'  THEN '52'
        			WHEN p1.value = '7966'  THEN '53'
        			WHEN p1.value = '7967'  THEN '54'
        			WHEN p1.value = '7968'  THEN '55'
        			ELSE 0
        		END AS value
        	, 1 
        	FROM ProductAttributes  p1 
        	WHERE p1.TYPE = 'categories' AND p1.value  IN ('3','43','44','62','65','66','67','7959','7960','7961','7962','7963','7964','7965','7966','7967','7968')
        ) t
        ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1;";
        $dbAdpr->query($sQuery)->execute();
        
        $dbAdpr->query( 'DROP TABLE IF EXISTS `ProductCategories_temp`;' )->execute();
        $dbAdpr->query( 'ALTER TABLE `ProductCategories` RENAME TO  `ProductCategories_temp` ;' )->execute();
        $dbAdpr->query( 'ALTER TABLE `ProductCategories_for_processing` RENAME TO  `ProductCategories` ;' )->execute();
        $dbAdpr->query( 'ALTER TABLE `ProductCategories_temp` RENAME TO  `ProductCategories_for_processing` ;' )->execute();      
        
        die;    
    }

}

