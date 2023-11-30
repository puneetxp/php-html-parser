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
    public function __construct(public string $htmlstring, public int $key = 0)
    {
        $this->html = str_split($htmlstring);
        $this->length = strlen($htmlstring);
    }

    public function parse()
    {
        $string = "";
        while ($this->length > $this->key) {
            $string .= $value;
            if ($value == "<") {
                if (preg_match("/[A-Za-z]/m", $this->html[$this->key + 1])) {
                    $this->addtag();
                }
                continue;
            }
            $this->key++;
        }
        return $this;
    }

    public function addtag()
    {
        $string = "";
        $ture = true;
        $attribute = [];
        while ($this->length > $this->key) {
            $string .= $this->html[$this->key];
            if (preg_match("/[A-Za-z\-\.]/", $string)) {
            } else {
                if ($this->html[$this->key] === " ") {
                    if (in_array($string, $this->selfClosing)) {
                        $this->key++;
                        [$this->key, $attribute] = $this->addattribute();
                        array_push($this->tags, ["tag" => $string, "closed" => true, "attribute" => $attribute]);
                    } else {
                        $this->key++;
                        [$this->key, $attribute] = $this->addattribute();
                    }
                } elseif ($this->html[$this->key] === "/>") {
                    array_push($this->tags, ["tag" => $string, "closed" => true, "attribute" => $attribute]);
                    $string = "";
                } elseif ($this->html[$this->key] === ">") {
                    $this->key++;
                    $child = (new smart($this->htmlstring, $this->key + 1))->parse();
                    $this->key = $child->key;
                    $this->tags[count($this->tags) - 1]["childern"] = $child->tags;
                } elseif ($this->html[$this->key] === "<") {
                    $this->addstring($string);
                    $string = "";
                }
                // $tag = ["tag" => $string];
            }
            $this->key++;
        }
    }
    public function addattribute()
    {
        $string = "";
        $attribute = [];
        $ture = true;
        while ($ture && $this->length > $this->key) {
            if (!$this->html[$this->key] = " ") {
                if (chop($string) !== "") {
                    $attribute[] = [$string => ["value" => "", "quote" => '']];
                }
            } elseif ($this->html[$this->key] = "=") {
                $value = "";
                if ($this->html[$this->key + 1] == '"') {
                    while ($this->html[$this->key] != '"') {
                        $this->key++;
                        $value .= $this->html[$this->key];
                    }
                    $attribute[] = [$string => ["value" => $value, "quote" => '"']];
                } elseif ($this->html[$this->key + 1] == "'") {
                    while ($this->html[$this->key] != "'") {
                        $this->key++;
                        $value .= $this->html[$this->key];
                    }
                    $attribute[] = [$string => ["value" => $value, "quote" => "'"]];
                } else {
                }
            }
            $string .= $this->html[$this->key];
            $this->key++;
        }
        return [$this->key, $attribute];
    }
    public function addstring(string $string)
    {
        array_push($this->tags, ["tag" => "", "string" => $string]);
    }
}
