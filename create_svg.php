<?php

class CreateSvgClass{

  private $strs = "";
  private $font_size = 8;

  private $contents = [];
  private $tmp_lines = [];

  const LINE_LENGTH = 30;
  const PAGE_LINES = 24;


  public function create(){
    // TODO:（とりあえず）外部ファイルから文字列読み込み
    $this->strs = file_get_contents('./sample.txt');
    

    $tmp_strs = explode("\n",$this->strs);
    
    $contents = [];

    $lines = [];
    
    // １行の処理とか
    foreach($tmp_strs as $row){
      if(mb_strlen($row) > self::LINE_LENGTH){

        $t = $this->mb_str_split($row,self::LINE_LENGTH);
        foreach($t as $tmp){
          $lines[] = $tmp;
        }
      }else{
        $lines[] = $row;
      }
    }

    //
    $lines_size = count($lines);
    foreach($lines as $i => $line){
      // 行頭禁則処理
      if(1 == preg_match('/^(,|\)|\]|｝|、|〕|〉|》|」|』|】|〙|〗|〟|\’|”|｠|»|ゝ|ゞ|ー|‐|゠|–|〜|～|\?|!|‼|\⁇|⁈|⁉|・|:|;|\/|。|\.|ァ|ィ|ゥ|ェ|ォ|ッ|ャ|ュ|ョ|ヮ|ヵ|ヶ|ぁ|ぃ|ぅ|ぇ|ぉ|っ|ゃ|ゅ|ょ|ゎ|ゕ|ゖ|ㇰ|ㇱ|ㇲ|ㇳ|ㇴ|ㇵ|ㇶ|ㇷ|ㇸ|ㇹ|ㇷ|゚|ㇺ|ㇻ|ㇼ|ㇽ|ㇾ|ㇿ|々|〻)/',$line,$m)){
        $lines[$i] = mb_substr($line,1);
        $lines[$i -1] .= $m[0];
      }
      //行末禁則処理
      if($lines_size > $i && 1 == preg_match('/(\(|\[|｛|〔|〈|《|「|『|【|〘|〖|〝|\‘|“|｟|«)$/',$lines[$i],$m)){
        $lines[$i] = mb_substr($lines[$i],0,mb_strlen($lines[$i]) - 1);
        $lines[$i +1] = $m[0] . $lines[$i + 1];
      }
      
    }
    foreach($lines as $line){
      $this->__addLine($line);
    }


      // svgファイル書き出し
      $fp = fopen('aiueo.svg','w');
      $svg_header = <<<EOT
      <svg xmlns="http://www.w3.org/2000/svg" width="210mm" height="297mm"  viewbox="0 0 2100 2970">
      
      <line x1="105mm" y1="0" x2="105mm" y2="297mm" stroke="#cccccc" stroke-width="1px"/>
      <line x1="105mm" y1="74.25mm" x2="105mm" y2="223mm" stroke="#333333" stroke-width="1px"/>
      <line x1="" y1="74.25mm" x2="210mm" y2="74.25mm" stroke="#cccccc" stroke-width="1"/>
      <line x1="" y1="148.5mm" x2="210mm" y2="148.5mm" stroke="#cccccc" stroke-width="1"/>
      <line x1="" y1="223mm" x2="210mm" y2="223mm" stroke="#cccccc" stroke-width="1"/>
EOT;
      fwrite($fp,$svg_header . PHP_EOL);

      $x = 0;
      
      // 1-4p
      foreach($this->contents as $i => $page){
        
        fwrite($fp,'<text transform="rotate(90 0 0) translate(' .$x . ',-350)" font-fammily="Shippori Mincho" font-size="8" stlye="padding:5px;">' . PHP_EOL);
        foreach($page as $n => $line){
          fwrite($fp,'<tspan x="10" y="' . $n *14 . 'px">'.$line.'</tspan>' . PHP_EOL);
        }

        fwrite($fp,'</text>' . PHP_EOL);
        // ノンブル
        fwrite($fp,'<text font-size="4" transform="rotate(90 0 0) translate(' .($x +130) . ',-10)">' . ($i +1) . '</text>');
        if($i == 3){
          break;
        }
        $x += 263;
      }

      // 表紙、裏表紙
      $x = -263;
      fwrite($fp,'<text transform="rotate(-90 0 0) translate('.$x.',395)" font-fammily="Shippori Mincho" font-size="8" stlye="padding:5px;">' . PHP_EOL);
      fwrite($fp,'<tspan x="10" y="230px">表紙</tspan>');
      fwrite($fp,'</text>' . PHP_EOL);
      $x += -263;
      fwrite($fp,'<text transform="rotate(-90 0 0) translate('.$x.',395)" font-fammily="Shippori Mincho" font-size="8" stlye="padding:5px;">' . PHP_EOL);
      fwrite($fp,'<tspan x="10" y="230px">裏表紙</tspan>');
      fwrite($fp,'</text>' . PHP_EOL);
      
      
      //6p 
      $x += -263;
      fwrite($fp,'<text transform="rotate(-90 0 0) translate('.$x.',395)" font-fammily="Shippori Mincho" font-size="8" stlye="padding:5px;">' . PHP_EOL);
      if(isset($this->contents[5]))
      foreach($this->contents[5] as $i => $line){
        fwrite($fp,'<tspan x="10" y="' . $i *14 . 'px">'.$line.'</tspan>' . PHP_EOL);
      }
      fwrite($fp,'</text>' . PHP_EOL);
      fwrite($fp,'<text font-size="4" transform="rotate(-90 0 0) translate(' .($x + 130) . ',730)">6</text>');
        // 5p
      $x += -263;
      fwrite($fp,'<text transform="rotate(-90 0 0) translate('.$x.',395)" font-fammily="Shippori Mincho" font-size="8" stlye="padding:5px;">' . PHP_EOL);
      if(isset($this->contents[4]))
      foreach($this->contents[4] as $i => $line){
        fwrite($fp,'<tspan x="10" y="' . $i *14 . 'px">'.$line.'</tspan>' . PHP_EOL);
      }
      fwrite($fp,'</text>' . PHP_EOL);
      fwrite($fp,'<text font-size="4" transform="rotate(-90 0 0) translate(' .($x + 130) . ',730)">5</text>');
      
      fwrite($fp,'</svg>' . PHP_EOL);
      fclose($fp);
    }

    private function __addLine($row){

      if(count($this->tmp_lines) == self::PAGE_LINES){
        $this->contents[] = $this->tmp_lines;
        $this->tmp_lines = [];
      }
      
      $this->tmp_lines[] = $row;
    }

    function mb_str_split($str, $split_len = 1) {
      
      mb_internal_encoding('UTF-8');
      mb_regex_encoding('UTF-8');
      
      if ($split_len <= 0) {
        $split_len = 1;
      }
      
      $strlen = mb_strlen($str, 'UTF-8');
      $ret  = array();
      for ($i = 0; $i < $strlen; $i += $split_len) {
        $ret[] = mb_substr($str, $i, $split_len);              
      }

      return $ret;
    }

}

$c = new CreateSvgClass();
$c->create();