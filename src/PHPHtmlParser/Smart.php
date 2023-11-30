<?php

namespace The\PHPHtmlParser;
class Smart
{
    public array $tags = [];
    public int $length;
    public $selfClosing = [
        'area',
        'base',
        'basefont',
        'br',
        'col',
        'embed',
        'hr',
        'img',
        'input',
        'keygen',
        'link',
        'meta',
        'param',
        'source',
        'spacer',
        'track',
        'wbr',
    ];
    public array $html;
    public string $active;
    public function __construct(public string $htmlstring, public int $point = 0)
    {
        $this->html = str_split($htmlstring);
        $this->length = strlen($htmlstring);
    }

    public function parse()
    {
        $string = "";
        foreach ($this->html as $key => $value) {
            if ($value == "<") {
                if (preg_match("/[A-Za-z]/m", $this->html[$key + 1])) {
                    $this->addtag($key);
                }
                continue;
            }
        }
        return $this;
    }

    public function addtag(int $key)
    {
        $string = "";
        $k = $key;
        $ture = true;
        $attribute = [];
        while ( $this->length > $key) {
            $string .= $this->html[$key];
            print_r($string);
            print_r("\n");
            if (preg_match("/[A-Za-z\-]/", $string)) {
            } else {
                if ($this->html[$key] === " ") {
                    if (in_array($string, $this->selfClosing)) {
                        [$key, $attribute] = $this->addattribute($key + 1);
                        array_push($this->tags, ["tag" => $string, "closed" => true, "attribute" => $attribute]);
                    } else {
                        [$key, $attribute] = $this->addattribute($key + 1);
                    }
                } elseif ($this->html[$key] === "/>") {
                    array_push($this->tags, ["tag" => $string, "closed" => true, "attribute" => $attribute]);
                    $string = "";
                } elseif ($this->html[$key] === ">") {
                    $child = (new smart($this->htmlstring, $key + 1))->parse();
                    $key = $child->point;
                    $this->tags[count($this->tags) - 1]["childern"] = $child->tags;
                } elseif ($this->html[$key] === "<") {
                    $this->addstring($string);
                    $string = "";
                }
                // $tag = ["tag" => $string];
            }
            $key++;
        }
    }
    public function addattribute(int $key)
    {
        $string = "";
        $attribute = [];
        $ture = true;
        while ($ture && $this->length > $key) {
            if (!$this->html[$key] = " ") {
                if (chop($string) !== "") {
                    $attribute[] = [$string => ["value" => "", "quote" => '']];
                }
            } elseif ($this->html[$key] = "=") {
                $value = "";
                if ($this->html[$key + 1] == '"') {
                    while ($this->html[$key] != '"') {
                        $key++;
                        $value .= $this->html[$key];
                    }
                    $attribute[] = [$string => ["value" => $value, "quote" => '"']];
                } elseif ($this->html[$key + 1] == "'") {
                    while ($this->html[$key] != "'") {
                        $key++;
                        $value .= $this->html[$key];
                    }
                    $attribute[] = [$string => ["value" => $value, "quote" => "'"]];
                } else {
                }
            }
            $string .= $this->html[$key];
            $key++;
        }
        return [$key, $attribute];
    }
    public function addstring(string $string)
    {
        array_push($this->tags, ["tag" => "", "string" => $string]);
    }
}
