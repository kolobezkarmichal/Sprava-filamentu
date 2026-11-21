<?php
if (!defined('FILAMENT_APP')) { http_response_code(404); exit; }
// Malý samostatný PDF export. Vložený Unicode font zaručuje českou diakritiku i bez CDN.
class FilamentPdf {
    private $metrics; private $used=[]; private $pages=[]; private $content=''; private $y=0;
    private $title; private $subtitle; private $headers; private $widths; private $pageNo=0;
    function __construct($title,$headers,$widths,$subtitle) {
        $this->metrics=require __DIR__.'/pdf-font.php';
        $this->title=$title;$this->subtitle=$subtitle;$this->headers=$headers;
        $total=array_sum($widths);$this->widths=array_map(function($w)use($total){return $w*769.89/$total;},$widths);
    }
    private function codepoint($char) {
        $b=array_values(unpack('C*',$char));$n=count($b);
        if($n===1)return $b[0];if($n===2)return (($b[0]&31)<<6)|($b[1]&63);
        if($n===3)return (($b[0]&15)<<12)|(($b[1]&63)<<6)|($b[2]&63);
        return 63;
    }
    private function chars($text) { return preg_split('//u',(string)$text,-1,PREG_SPLIT_NO_EMPTY)?:[]; }
    private function cp($char) { $cp=$this->codepoint($char);return isset($this->metrics[$cp])?$cp:63; }
    private function measure($text,$size) {
        $w=0;foreach($this->chars($text) as $c)$w+=$this->metrics[$this->cp($c)][1]*$size/1000;return $w;
    }
    private function text($x,$y,$text,$size=9) {
        $hex='';foreach($this->chars($text) as $c){$cp=$this->cp($c);$this->used[$cp]=true;$hex.=sprintf('%04X',$cp);}
        $this->content.=sprintf("BT /F1 %.2F Tf 1 0 0 1 %.2F %.2F Tm <%s> Tj ET\n",$size,$x,595.28-$y,$hex);
    }
    private function wrap($text,$width,$size=9) {
        $out=[];
        foreach(explode("\n",str_replace(["\r\n","\r"],"\n",(string)$text)) as $paragraph) {
            $line='';
            foreach(preg_split('/\s+/u',trim($paragraph),-1,PREG_SPLIT_NO_EMPTY) as $word) {
                $trial=$line===''?$word:$line.' '.$word;
                if($this->measure($trial,$size)<=$width){$line=$trial;continue;}
                if($line!==''){$out[]=$line;$line='';}
                foreach($this->chars($word) as $c){if($line!==''&&$this->measure($line.$c,$size)>$width){$out[]=$line;$line='';}$line.=$c;}
            }
            $out[]=$line;
        }
        return $out?:[''];
    }
    private function rect($x,$y,$w,$h,$fill=false) {
        $this->content.=sprintf("%.2F %.2F %.2F %.2F re %s\n",$x,595.28-$y-$h,$w,$h,$fill?'f':'S');
    }
    private function newPage() {
        if($this->content!=='')$this->pages[]=$this->content;
        $this->content='0.13 0.16 0.27 rg 0.82 0.85 0.90 RG 0.4 w' . "\n";$this->pageNo++;
        $this->text(36,34,$this->title,17);
        $this->text(36,51,'Vytvořeno: '.date('d.m.Y H:i').' · Europe/Prague',9);
        $this->y=66;
        foreach($this->wrap($this->subtitle,769,8) as $line){$this->text(36,$this->y,$line,8);$this->y+=11;}
        $this->y+=5;$this->content.="0.93 0.94 0.98 rg\n";$this->rect(36,$this->y,769.89,27,true);$this->content.="0.13 0.16 0.27 rg\n";
        $x=36;foreach($this->headers as $i=>$head){$this->text($x+5,$this->y+17,$head,8);$x+=$this->widths[$i];}
        $this->y+=27;
        $this->text(36,575,'Moje filamenty',8);$this->text(740,575,'Strana '.$this->pageNo,8);
    }
    function addRows($rows) {
        $this->newPage();
        if(!$rows){$this->text(41,$this->y+22,'Žádné záznamy pro zvolené filtry.',10);return;}
        foreach($rows as $row){
            $cells=[];$max=1;
            foreach($row as $i=>$value){$cells[$i]=$this->wrap($value,$this->widths[$i]-10);$max=max($max,count($cells[$i]));}
            $offset=0;
            while($offset<$max){
                $available=(int)floor((548-$this->y-10)/12);
                // Běžný řádek zůstává na jedné straně; mimořádně dlouhý se bezpečně rozdělí.
                if($available<1||($offset===0&&$max>$available&&$max<=30)){$this->newPage();$available=(int)floor((548-$this->y-10)/12);}
                $take=min($max-$offset,$available);$height=$take*12+10;$x=36;
                foreach($cells as $i=>$lines){
                    $this->rect($x,$this->y,$this->widths[$i],$height);
                    for($j=0;$j<$take;$j++)if(isset($lines[$offset+$j]))$this->text($x+5,$this->y+14+$j*12,$lines[$offset+$j]);
                    $x+=$this->widths[$i];
                }
                $this->y+=$height;$offset+=$take;
                if($offset<$max)$this->newPage();
            }
        }
    }
    function bytes() {
        $this->pages[]=$this->content;
        $font=file_get_contents(__DIR__.'/files/DejaVuSans.ttf');if($font===false)throw new RuntimeException('Chybí font PDF. Nahraj i soubor files/DejaVuSans.ttf.');
        $objects=[];
        $add=function($text)use(&$objects){$objects[]=$text;return count($objects);};
        $stream=function($data,$extra='') { $z=gzcompress($data);return '<< /Length '.strlen($z).' /Filter /FlateDecode '.$extra.">>\nstream\n".$z."\nendstream"; };
        $add('<< /Type /Catalog /Pages 2 0 R >>');$add('');
        $add('<< /Type /Font /Subtype /Type0 /BaseFont /DejaVuSans /Encoding /Identity-H /DescendantFonts [4 0 R] /ToUnicode 7 0 R >>');
        ksort($this->used);$widths='';$map=str_repeat("\0",131072);$bf=[];
        foreach($this->used as $cp=>$unused){$metric=$this->metrics[$cp];$widths.=$cp.' ['.$metric[1].'] ';$gid=pack('n',$metric[0]);$map[$cp*2]=$gid[0];$map[$cp*2+1]=$gid[1];$bf[]=sprintf('<%04X> <%04X>',$cp,$cp);}
        $add('<< /Type /Font /Subtype /CIDFontType2 /BaseFont /DejaVuSans /CIDSystemInfo << /Registry (Adobe) /Ordering (Identity) /Supplement 0 >> /FontDescriptor 5 0 R /CIDToGIDMap 8 0 R /DW 600 /W ['.$widths.'] >>');
        $add('<< /Type /FontDescriptor /FontName /DejaVuSans /Flags 32 /FontBBox [-1021 -463 1793 1232] /ItalicAngle 0 /Ascent 928 /Descent -236 /CapHeight 729 /StemV 80 /FontFile2 6 0 R >>');
        $add($stream($font,'/Length1 '.strlen($font).' '));
        $cmap="/CIDInit /ProcSet findresource begin\n12 dict begin\nbegincmap\n/CIDSystemInfo << /Registry (Adobe) /Ordering (UCS) /Supplement 0 >> def\n/CMapName /Adobe-Identity-UCS def\n/CMapType 2 def\n1 begincodespacerange\n<0000> <FFFF>\nendcodespacerange\n";
        foreach(array_chunk($bf,100) as $chunk)$cmap.=count($chunk)." beginbfchar\n".implode("\n",$chunk)."\nendbfchar\n";
        $cmap.="endcmap\nCMapName currentdict /CMap defineresource pop\nend\nend";
        $add($stream($cmap));$add($stream($map));$kids=[];
        foreach($this->pages as $content){$cid=$add($stream($content));$pid=$add('<< /Type /Page /Parent 2 0 R /MediaBox [0 0 841.89 595.28] /Resources << /Font << /F1 3 0 R >> >> /Contents '.$cid.' 0 R >>');$kids[]=$pid.' 0 R';}
        $objects[1]='<< /Type /Pages /Count '.count($kids).' /Kids ['.implode(' ',$kids).'] >>';
        $pdf="%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";$offsets=[0];
        foreach($objects as $i=>$object){$offsets[]=strlen($pdf);$pdf.=($i+1)." 0 obj\n".$object."\nendobj\n";}
        $start=strlen($pdf);$pdf.="xref\n0 ".count($offsets)."\n0000000000 65535 f \n";
        foreach(array_slice($offsets,1) as $offset)$pdf.=sprintf("%010d 00000 n \n",$offset);
        return $pdf.'trailer << /Size '.count($offsets).' /Root 1 0 R >>'."\nstartxref\n".$start."\n%%EOF\n";
    }
}
function exportPdf($title,$headers,$rows,$widths,$filter,$filename) {
    if(!function_exists('gzcompress'))throw new RuntimeException('Pro PDF export musí hosting zapnout rozšíření zlib.');
    $pdf=new FilamentPdf($title,$headers,$widths,count($rows).' záznamů'.($filter!==''?' · '.$filter:''));$pdf->addRows($rows);$bytes=$pdf->bytes();
    header('Content-Type: application/pdf');header('Content-Disposition: attachment; filename="'.$filename.'.pdf"');header('Content-Length: '.strlen($bytes));echo $bytes;exit;
}
