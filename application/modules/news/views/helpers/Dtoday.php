<?php
use Pandamp\Utility\Hijriah;
class News_View_Helper_Dtoday
{
	public function dtoday($date=NULL)
	{
		if (isset($date)) {
			$h = date("N",strtotime($date));
			$i = date("j",strtotime($date));
			$j = date("n",strtotime($date));
			$k = date("Y",strtotime($date));
			$l = date('d',strtotime($date));
			$m = date('m',strtotime($date));
		}
		else 
		{
			$h = date("N");
			$i = date("j");
			$j = date("n");
			$k = date("Y");
			$l = date('d');
			$m = date('m');
		}
		
		$array_hari = array(1=>"Senin","Selasa","Rabu","Kamis","Jumat","Sabtu","Minggu");
		$hari = $array_hari[$h];
		$tanggal = $i;
		$array_bulan = array(1=>"Januari","Februari","Maret", "April", "Mei", "Juni","Juli","Agustus","September","Oktober", "November","Desember");
		$bulan = $array_bulan[$j];
		$tahun = $k;
			
		$hijriah = new Hijriah();
		$dateHijri = $hijriah->g2u($l,$m,$k);
			
		$array_bulan_hijriah = array(1=>"Muharram","Safar","Rabiul awal","Rabiul akhir","Jumadil awal","Jumadil akhir","Rajab","Sya'ban","Ramadhan","Syawal","Dzulkaidah","Dzulhijjah");
		$bulanHijri = $array_bulan_hijriah[$dateHijri['month']];
			
		$today = $hari . ', ' . $dateHijri['day'] . ' ' . $bulanHijri . ' ' . $dateHijri['year'] . ' ' .
				' | ' . $tanggal . ' ' . $bulan . ' ' . $tahun;
		
		return $today;
	}
}