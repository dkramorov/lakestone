<?php
ini_set('max_execution_time', 0);
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
$host = "localhost";
$user = "vet1987_lake";
$pass = "jUPVInds";
$db = "vet1987_lake";

// Configuration
if (is_file('config.php')) {
  require_once('config.php');
  $mysqli = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
} else {
  $mysqli = new mysqli($host, $user, $pass, $db);
}
$mysqli->set_charset("utf8");
/* if(isset($_GET['test']) && $_GET['test']=='tatikatest')
    print_r(getProductAttributes()); */
$arGoogleCats = array(
"60" => "5181",
"61" => "100",
"62" => "101",
"63" => "5181",
"64" => "5650",
"65" => "100",
"66" => "6551",
"67" => "100",
"71" => "5181",
"72" => "5181",
"73" => "5181",
);
#$y = fopen("/home/v/vet1987/lakestone.ru/public_html/yandex_market.yml", "w");
$y = fopen("/dev/null", "w");
$g = fopen("googlemerchants.xml", "w");
#$f = fopen("/home/v/vet1987/lakestone.ru/public_html/facebook.xml", "w");
$f = fopen("/dev/null", "w");
$strout = '<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<yml_catalog date="'.date("Y-m-d H:i").'">
<shop>
<name>LakeStone</name>
<company>LakeStone - кожаные сумки, портфели, папки для документов, рюкзаки и аксессуары</company>
<url>https://lakestone.ru</url>
<currencies>
    <currency id="RUB" rate="1"/>
</currencies>
<categories>
';
fputs($y, $strout);
$strout = '<?xml version="1.0"?>
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
<channel>
<title>LakeStone</title>
<link>https://lakestone.ru</link>
<description>LakeStone - кожаные сумки, портфели, папки для документов, рюкзаки и аксессуары</description>
';
fputs($g, $strout);
fputs($f, $strout);
$res = $mysqli->query("SELECT `oc_category`.`category_id`, `oc_category_description`.`name` FROM `oc_category`, `oc_category_description` WHERE `oc_category_description`.`category_id` = `oc_category`.`category_id` AND `oc_category`.`category_id` > 59");
$arAllCats = array();
while($ar = $res->fetch_assoc()){
	if ($ar["category_id"] == 63) {
		$ar["name"] = "Сумки-папки";
	}
	$strout = "\t<category id=\"".$ar["category_id"]."\">".$ar["name"]."</category>\n";
	fputs($y, $strout);
	$arAllCats[$ar["category_id"]] = $ar["name"];
	//print_r($ar);
}
$strout = '</categories>
<delivery-options>
	<option cost="0" days="0-1"/>
</delivery-options>
<offers>
';
fputs($y, $strout);
//$res = $mysqli->query("SELECT `oc_product`.`quantity`, `oc_product`.`stock_status_id`, `oc_product`.`product_id`, `oc_product`.`model`, `oc_product`.`dimension`, `oc_product`.`compatibility`, `oc_product`.`material`, `oc_product`.`strap`, `oc_product`.`storage`, `oc_product`.`attribute1`, IFNULL(`oc_product_special`.`price`, `oc_product`.`price`) AS price, `oc_product`.`image`, MIN(`oc_product_to_category`.`category_id`) AS category_id, `oc_product_description`.`name`, `oc_product_description`.`description` FROM `oc_product`, `oc_product_to_category`, `oc_product_description`, `oc_product_special` WHERE `oc_product_to_category`.`product_id` = `oc_product`.`product_id` AND `oc_product_description`.`product_id` = `oc_product`.`product_id` AND `oc_product_special`.`product_id` = `oc_product`.`product_id` AND `oc_product_special`.`date_start` < NOW() AND (`oc_product_special`.`date_end` = '0000-00-00' OR `oc_product_special`.`date_end` > NOW()) AND `oc_product_to_category`.`category_id` > 59 GROUP BY `product_id`");
$res = $mysqli->query("SELECT `oc_product`.`quantity`, `oc_product`.`product_id`, `oc_product`.`model`, `oc_product`.`price`, `oc_product`.`image`, MIN(`oc_product_to_category`.`category_id`) AS category_id, `oc_product_description`.`name`, `oc_product_description`.`description` FROM `oc_product`, `oc_product_to_category`, `oc_product_description` WHERE `oc_product_to_category`.`product_id` = `oc_product`.`product_id` AND `oc_product_description`.`product_id` = `oc_product`.`product_id` AND `oc_product_to_category`.`category_id` > 59 GROUP BY `product_id`");
//`oc_product`.`dimension`, `oc_product`.`compatibility`, `oc_product`.`material`, `oc_product`.`strap`, `oc_product`.`storage`, `oc_product`.`attribute1`, 
while($ar = $res->fetch_assoc()){
    $properties = array_reverse(getProductAttributes($ar["product_id"]));
	$p = (float)$ar["price"];
	$utm_campaign = $utm_content = ($p>50000)?('50001'):(($p>20000)?(getRangeByStep($p,10000)):(($p>10000)?getRangeByStep($p,5000):($p>5000?getRangeByStep($p,2500):($p>1000?getRangeByStep($p,1000):'0_1000'))));
	$resImg = $mysqli->query("SELECT `image` FROM `oc_product_image` WHERE `product_id` = ".$ar["product_id"]);
	$arImages = array();
	$arImages[] = "https://www.lakestone.ru/image/".str_ireplace(" ", "%20", $ar['image']);
	if ($resImg) {
		while ($arImg = $resImg->fetch_assoc() and count($arImages) < 5){
			if ($arImg['image'] != "catalog/box.jpg") $arImages[] = "https://www.lakestone.ru/image/".str_ireplace(" ", "%20", $arImg['image']);
		}
	}
	if ($ar["category_id"] == 63)
		$ar["name"] = str_ireplace("Папка", "Сумка-папка", $ar["name"]);
	$ar["name"] = str_ireplace("Деловая сумка ", "Портфель - деловая сумка ", $ar["name"]);
	$name = preg_split("/[a-zA-Z]+/", $ar["name"])[0];
	$model = str_ireplace($name, "", $ar["name"]);
	$color = @$mysqli->query("SELECT text FROM oc_product_attribute WHERE attribute_id = 18 AND product_id = ".$ar["product_id"])->fetch_object()->text;
	$ar["description"] = trim(str_ireplace("&nbsp;", " ",strip_tags(htmlspecialchars_decode($ar["description"]))));
	$nameG = $nameY = $nameF = $name."LakeStone ".$model." ".$color." ".$ar["model"];
	if (strlen($nameF)>150)
		$nameG = $name."LakeStone ".$model." ".$color." ".$ar["model"];
	if (strlen($nameF)>120)
		$nameY = $name."LakeStone ".$model." ".$color." ".$ar["model"];
	$availability = ( $ar["quantity"] > 0 ) ? "true" : "false";
	$strout = "\t<offer id=\"".$ar["product_id"]."\" available=\"".$availability."\">\n";
	$strout .= "\t\t<url>".getUrl($ar["product_id"], $ar["category_id"])."?utm_source=yandex_market&amp;utm_medium=cpc&amp;utm_campaign=".encodestring($arAllCats[$ar["category_id"]]."&amp;utm_term=".$ar["product_id"]."&amp;utm_content=".$utm_content)."</url>\n";
	$strout .= "\t\t<price>".sprintf("%.2f", $ar["price"])."</price>\n";
	$strout .= "\t\t<currencyId>RUB</currencyId>\n";
	$strout .= "\t\t<categoryId>".$ar["category_id"]."</categoryId>\n";
	foreach ($arImages as $img) {
		$strout .= "\t\t<picture>".$img."</picture>\n";
	}
	$strout .= "\t\t<store>false</store>\n";
	$strout .= "\t\t<pickup>true</pickup>\n";
	$strout .= "\t\t<delivery>true</delivery>\n";
	$params = "";
	if ($ar["name"] == "Сумка-папка Elmdale Black" || $ar["name"] == "Сумка-папка Elton Black" || $ar["name"] == "Сумка-папка Crosby Black"){
		$nameY = str_ireplace("Сумка-папка", "Папка-портфель", $nameY);
		$params = "\t\t<param name=\"Тип\">папка для документов</param>";
	}
	$strout .= "\t\t<name>".$nameY."</name>\n";
	$strout .= "\t\t<model>".$model."</model>\n";
	$strout .= "\t\t<vendor>LakeStone</vendor>\n";
	$strout .= "\t\t<vendorCode>".$ar["model"]."</vendorCode>\n";
	$strout .= "\t\t<description>".$ar["description"]."</description>\n";
	$strout .= "\t\t<sales_notes>Оплата при получении</sales_notes>\n";
	$strout .= "\t\t<country_of_origin>Россия</country_of_origin>\n";
	$strout .= "\t\t<manufacturer_warranty>true</manufacturer_warranty>\n";
	$strout .= "\t\t<delivery-options>\n\t\t\t<option cost=\"0\" days=\"". (( $ar["quantity"] > 0 ) ? "0-1" : "32" )."\"/>\n\t\t</delivery-options>\n";
	$strout .= $params;
    if ($ar["category_id"] == 62)
        $strout .= "\t\t<param name=\"Способ ношения\">на плече</param>\n\t\t<param name=\"Способ ношения\">через плечо</param>\n";
	$strout .= "\t\t<param name=\"Материал\">натуральная кожа</param>\n";
	$strout .= "\t\t<param name=\"Цвет\">".$color."</param>\n";
#	if($ar["category_id"] == 65)
#		$strout .= "\t\t<param name=\"Пол\">для женщин</param>\n";
#	else
#		$strout .= "\t\t<param name=\"Пол\">для мужчин</param>\n";
	$strout .= "\t</offer>\n";
	fputs($y, $strout);
	$strout = "<item>\n";
	$strout .= "\t<g:id>".$ar["product_id"]."</g:id>\n";
	$strout .= "\t<g:custom_label_0>".$utm_campaign."</g:custom_label_0>\n";
	$strout .= "\t<title>".$nameG."</title>\n";
	#$strout .= "\t<link>".getUrl($ar["product_id"], $ar["category_id"])."?utm_source=google_merchants&amp;utm_medium=cpc&amp;utm_campaign=".$utm_campaign."&amp;utm_term=".$ar["product_id"]."&amp;utm_content=".encodestring($arAllCats[$ar["category_id"]])."</link>\n";
	$strout .= "\t<link>" . getUrl($ar["product_id"], $ar["category_id"]) . "</link>\n";
    foreach ($properties as $name => $value){
        $ar["description"] = $name . ': ' . $value . '; ' . $ar["description"];
    }
	$strout .= "\t<description>".$ar["description"]."</description>\n";
	$strout .= "\t<g:image_link>".$arImages[0]."</g:image_link>\n";
	for($i=1;$i<=10;$i++){
		if 	(isset($arImages[$i]))
			$strout .= "\t<g:additional_image_link >".$arImages[$i]."</g:additional_image_link >\n";
	}
	$availability = ( $ar["quantity"] > 0 ) ? "in stock" : "out of stock";
	$strout .= "\t<g:availability>".$availability."</g:availability>\n";
	$strout .= "\t<g:price>".sprintf("%.2f", $ar["price"])." RUB</g:price>\n";
	$strout .= "\t<g:condition>new</g:condition>\n";
	$strout .= "\t<g:product_type>".$arAllCats[$ar["category_id"]]."</g:product_type>\n";
	if (isset($arGoogleCats[$ar["category_id"]]))
		$strout .= "\t<g:google_product_category>".$arGoogleCats[$ar["category_id"]]."</g:google_product_category>\n";
	$strout .= "\t<g:brand>LakeStone</g:brand>\n";
	$strout .= "\t<g:color>".$color."</g:color>\n";
	if($ar["category_id"] == 65)
		$strout .= "\t<g:gender>женский</g:gender>\n";
	else
		$strout .= "\t<g:gender>мужской</g:gender>\n";
	$strout .= "\t<g:material>Натуральная кожа</g:material>\n";
	$strout .= "</item>\n";
	fputs($g, $strout);
	$strout = preg_replace('/^.*g:gender.*$/m', '', $strout);
	fputs($f, $strout);
	//print_r($ar);
}
$strout = '</offers>
</shop>
</yml_catalog>
';
fputs($y, $strout);
$strout = '</channel>
</rss>
';
fputs($g, $strout);
fputs($f, $strout);
if (isset($_GET['file']) && ($_GET['file'] == 'yandex_market.yml' || $_GET['file'] == 'googlemerchants.xml')){
    $file = $_GET['file'];
    if (file_exists($file)) {
    header("Content-type: text/xml; charset=utf-8");
    echo file_get_contents($file);
    exit;
  }
}
function getRangeByStep($p,$s){
	if (floor($p/$s)!=ceil($p/$s)){
		return (floor($p/$s)*$s+1)."_".(ceil($p/$s)*$s);
	} else {
		return (floor($p/$s)*$s+1)."_".((ceil($p/$s)+1)*$s);
	}
}
function encodestring($st) {
	$replace=array(
	"'"=>"",
	","=>"",
	"`"=>"",
	" "=>"_",
	"а"=>"a","А"=>"a",
	"б"=>"b","Б"=>"b",
	"в"=>"v","В"=>"v",
	"г"=>"g","Г"=>"g",
	"д"=>"d","Д"=>"d",
	"е"=>"e","Е"=>"e",
	"ж"=>"zh","Ж"=>"zh",
	"з"=>"z","З"=>"z",
	"и"=>"i","И"=>"i",
	"й"=>"y","Й"=>"y",
	"к"=>"k","К"=>"k",
	"л"=>"l","Л"=>"l",
	"м"=>"m","М"=>"m",
	"н"=>"n","Н"=>"n",
	"о"=>"o","О"=>"o",
	"п"=>"p","П"=>"p",
	"р"=>"r","Р"=>"r",
	"с"=>"s","С"=>"s",
	"т"=>"t","Т"=>"t",
	"у"=>"u","У"=>"u",
	"ф"=>"f","Ф"=>"f",
	"х"=>"h","Х"=>"h",
	"ц"=>"c","Ц"=>"c",
	"ч"=>"ch","Ч"=>"ch",
	"ш"=>"sh","Ш"=>"sh",
	"щ"=>"sch","Щ"=>"sch",
	"ъ"=>"","Ъ"=>"",
	"ы"=>"y","Ы"=>"y",
	"ь"=>"","Ь"=>"",
	"э"=>"e","Э"=>"e",
	"ю"=>"yu","Ю"=>"yu",
	"я"=>"ya","Я"=>"ya",
	"і"=>"i","І"=>"i",
	"ї"=>"yi","Ї"=>"yi",
	"є"=>"e","Є"=>"e"
	);
	return $st=iconv("UTF-8","UTF-8//IGNORE",strtr($st,$replace));
}
function getUrl($pId, $catId){
    global $mysqli;
    $catAlias = $mysqli->query("SELECT `keyword` FROM `oc_url_alias` WHERE `query` = 'category_id=$catId'")->fetch_assoc()['keyword'] ?? '';
    $pAlias = $mysqli->query("SELECT `keyword` FROM `oc_url_alias` WHERE `query` = 'product_id=$pId'")->fetch_assoc()['keyword'] ?? '';
    return "https://lakestone.ru/$catAlias/$pAlias";
}
function getProductAttributes($product_id = 184) {
    global $mysqli;
    $product_attribute_group_data = array();

    $product_attribute_group_query = $mysqli->query("SELECT ag.attribute_group_id, agd.name FROM oc_product_attribute pa LEFT JOIN oc_attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN oc_attribute_group ag ON (a.attribute_group_id = ag.attribute_group_id) LEFT JOIN oc_attribute_group_description agd ON (ag.attribute_group_id = agd.attribute_group_id) WHERE pa.product_id = '" . (int)$product_id . "' AND agd.language_id = 2 GROUP BY ag.attribute_group_id ORDER BY ag.sort_order, agd.name");

    while ($product_attribute_group = $product_attribute_group_query->fetch_assoc()) {
        $product_attribute_data = array();

        $product_attribute_query = $mysqli->query("SELECT a.attribute_id, ad.name, pa.text FROM oc_product_attribute pa LEFT JOIN oc_attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN oc_attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE pa.product_id = '" . (int)$product_id . "' AND a.attribute_group_id = '" . (int)$product_attribute_group['attribute_group_id'] . "' AND ad.language_id = 2 AND pa.language_id = 2 ORDER BY a.sort_order, ad.name");

        while ($product_attribute = $product_attribute_query->fetch_assoc()) {
            $product_attribute_group_data[$product_attribute['name']] = $product_attribute['text'];
        }
    }

    return $product_attribute_group_data;
}

/* class FeedBilder {
    private $feed_type;
    private $products = array();
    public function __construct($feed_type = "yandex"){
        if (in_array($feed_type, array("yandex", "google")))
            $this->feed_type = $feed_type;
        else
            $this->feed_type = "yandex";
    }
    public function setProduct(array $product){
        if (!isset($product['id']) ||
            !isset($product['name'])
            )
            return;
        $this->products[] = $product;
        
    }
} */
?>
