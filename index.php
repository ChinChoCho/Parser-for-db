<head>

   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

</head>
<?php
require 'phpQuery.php';


function get_content($currentUrl)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $currentUrl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$res = curl_exec($ch);
	curl_close($ch);
	return $res;
}

function parser($url, $folder, $category_id, $start, $end)
{
	require 'db.php';
	if ($start < $end) {
		$currentUrl = $url . $folder;
		//$file = file_get_contents($currentUrl);
		$file = get_content($currentUrl);
		$doc = phpQuery::newDocument($file);
		foreach ($doc->find(".catalog_item") as $article) {
			$article = pq($article);

			$array = [
				"id" => 0,
				"category_id" => $category_id,
				"title" => "",
				"description" => "",
				"price_new" => "",
				"price_old" => "",
				"image" => "",
				"status" => 1,
				"sticker_new" => 0
			];

			$array["image"] = $article->find(".image_wrapper_block img")->attr('src');
			$array["image"] = substr($currentUrl, 0, 17) . $array["image"];

			$array["title"] = str_replace('"', "''", $article->find(".item-title a span")->html());

			$array["description"] = str_replace('"', "''", $article->find(".my_extra_descr")->html());

			$existence = $article->find(".item-stock span.value")->html();
			if ($existence != "Есть в наличии") {
				$array["status"] = 0;
			}

			$array["price_new"] = substr(str_replace(" ₽", "", strstr($article->find(".price")->html(), "<", true)), 1);

			$array["price_old"] = str_replace(" ₽", "", $article->find(".price.discount span")->html());

			$stickerNewExistence = $article->find(".sticker_new");
			if (strlen($stickerNewExistence) > 0) {
				$array["sticker_new"] = 1;
			}

			echo '<img src="' . $array["image"] . '">';
			echo "<hr>";
			echo $array["title"];
			echo "<hr>";
			echo $array["description"];
			echo "<hr>";
			echo $array["status"];
			echo "<hr>";
			echo $array["price_new"];
			echo "<hr>";
			echo $array["price_old"];
			echo "<hr>";
			echo $array["sticker_new"];
			echo "<hr>";
			echo "<hr>";
			echo "<hr>";

			add_to_sql($db, $array);
		}
		$next = $doc->find(".nums .cur")->next()->attr('href');
		if (!empty($next)) {
			$start++;
			parser($next, "", $category_id, $start, $end);
		}
	}
}

$url = "https://andpro.ru";

$start = 0;
$end = 3;
// "/catalog/workstations/"

// parser($url, "/catalog/servers/server/", 8, $start, $end);
// parser($url, "/catalog/servers/server_boards/", 9, $start, $end);
// parser($url, "/catalog/servers/server_chassis/", 10, $start, $end);
// parser($url, "/catalog/servers/details_for_servers/", 11, $start, $end);
// parser($url, "/catalog/network/switch/", 12, $start, $end);
// parser($url, "/catalog/network/marshrutizatory/", 13, $start, $end);
// parser($url, "/catalog/network/firewall/", 14, $start, $end);
// parser($url, "/catalog/network/access_point/", 15, $start, $end);
// parser($url, "/catalog/network/usb_wireless/", 16, $start, $end);
// parser($url, "/catalog/network/cables_sfp/", 17, $start, $end);
// parser($url, "/catalog/computer_hardware/cases/", 18, $start, $end);
// parser($url, "/catalog/computer_hardware/servers_motherboards/", 19, $start, $end);
// parser($url, "/catalog/computer_hardware/cpu/", 20, $start, $end);
// parser($url, "/catalog/computer_hardware/memory_ram/", 21, $start, $end);
// parser($url, "/catalog/computer_hardware/hdd/", 22, $start, $end);
// parser($url, "/catalog/computer_hardware/ssd/", 23, $start, $end);
// parser($url, "/catalog/computer_hardware/vga_cards/", 24, $start, $end);
// parser($url, "/catalog/computer_hardware/nic/", 25, $start, $end);
// parser($url, "/catalog/power_supply/ups/", 26, $start, $end);
// parser($url, "/catalog/power_supply/pdu/", 27, $start, $end);
// parser($url, "/catalog/power_supply/surge_protector/", 28, $start, $end);
// parser($url, "/catalog/power_supply/power_cords/", 29, $start, $end);
// parser($url, "/catalog/storage/rack_storage/", 30, $start, $end);
// parser($url, "/catalog/storage/nas_storage/", 31, $start, $end);
// parser($url, "/catalog/storage/tape_storage/", 32, $start, $end);
// parser($url, "/catalog/storage/disk_enclosure/", 33, $start, $end);
// parser($url, "/catalog/soft/", 4, $start, $end);
// parser($url, "/catalog/workstations/", 7, $start, $end);

function add_to_sql($db, $array)
{
	$sql = 'INSERT INTO `products`(`id`, `category_id`, `title`, `description`, `price_new`, `price_old`, `image`, `status`, `sticker_new`) 
	VALUES (' . $array["id"] . ',' . $array["category_id"] . ',"' . $array["title"] . '","' . $array["description"] . '","' . $array["price_new"] . '","' . $array["price_old"] . '","' . $array["image"] . '",' . $array["status"] . ',' . $array["sticker_new"] . ')';

	// $rs = mysqli_query($db, $sql);
	if (mysqli_query($db, $sql)) {
		echo "New record created successfully";
	} else {
		echo "Error: " . $sql . "<br>" . mysqli_error($db);
	}
	// mysqli_close($db);
}

?>