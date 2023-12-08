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
    public string $active;

    public function __construct(public string $htmlstring = "", public int $key = 0, public array $html = [])
    {
        if (count($this->html)) {
            $this->length = count($this->html);
        } else {
            $this->html = str_split($htmlstring);
            $this->length = strlen($htmlstring);
        }
    }

    private $activetag;

    public function parse()
    {
        $string = "";
        while (
            $this->length > $this->key &&
            $this->html[$this->key + 1] ?? "" . $this->html[$this->key + 2] ?? "" !== "</"
        ) {
            if ($this->checktagisopen()) {
                $this->next();
                $this->checktag();
            } else {
                $string .= $this->html[$this->key];
                $this->next();
            }
        }
        if (isset($this->activetag["status"])) {
            while ($this->html[$this->key - 1] !== ">" && $this->activetag["status"] !== "close") {
                $this->checktag();
                $this->next();
            }
        }
        return $this;
    }

    private function checktag()
    {
        while (preg_match("/[A-Za-z\-\.]/m", $this->html[$this->key])) {
            $this->settag();
            $this->next();
        }
        $this->activetag["status"] = "pending";
        while ($this->activetag && isset($this->activetag["status"]) && $this->activetag["status"] == "pending") {
            if (!$this->checktagisopen()) {
                if ($this->html[$this->key] === " ") {
                    $this->next();
                    $this->addattribute();
                } elseif ($this->html[$this->key] . $this->html[$this->key + 1] === "/>") {
                    $this->closetag();
                } elseif ($this->html[$this->key] === ">") {
                    $this->closetag();
                    if ($this->activetag["status"] !== "closed") {
                        $this->next();
                        $child = (new smart(key: $this->key, html: $this->html))->parse();
                        $this->next("child", key: $child->key);
                        $this->tags[count($this->tags) - 1]["childern"] = $child->tags;
                    }
                }
                $this->next();
            }
        }
    }

    public function addattribute()
    {
        $attribute = "";
        $this->activetag["attribute"] = [];
        while ($this->activetag && isset($this->activetag["status"]) && $this->activetag["status"] !== "open" && $this->activetag["status"] !== "closed") {
            if (!$this->checktagisopen()) {
                if ($this->html[$this->key] == "=") {
                    $this->next("equal");
                    $value = "";
                    if ($this->html[$this->key] == "'" || $this->html[$this->key] == '"') {
                        while ($this->html[$this->key] == "'" || $this->html[$this->key] == '"') {
                            if ($this->html[$this->key] == '"') {
                                $this->next();
                                $this->activetag["attribute"][$attribute]["quote"] = '"';
                                while ($this->html[$this->key] != '"') {
                                    $value .= $this->html[$this->key];
                                    $this->next();
                                    $this->activetag["attribute"][$attribute]["value"] = $this->activetag["attribute"][$attribute]["value"] ?? "" .  $this->html[$this->key];
                                }
                            } elseif ($this->html[$this->key] == "'") {
                                $this->next();
                                $this->activetag["attribute"][$attribute]["quote"] = "'";
                                while ($this->html[$this->key] != "'") {
                                    $value .= $this->html[$this->key];
                                    $this->next();
                                    $this->activetag["attribute"][$attribute]["value"] = $this->activetag["attribute"][$attribute]["value"] ?? "" .  $this->html[$this->key];
                                }
                            }
                        }
                    } else {
                        while (!($this->html[$this->key] == " " && $this->html[$this->key] == ">") && $this->activetag["status"] !== "open" && $this->activetag["status"] !== "closed") {
                            if (!$this->checktagisopen()) {
                                $this->activetag["attribute"][$attribute]["value"] = $this->activetag["attribute"][$attribute]["value"] ?? "" .  $this->html[$this->key];
                            }
                            $this->next();
                        }
                        //print_r($this->activetag);
                    }
                    $attribute = "";
                } elseif ($this->html[$this->key] == " ") {
                    if (chop($attribute) !== "") {
                        $this->activetag["attribute"][] = [chop($attribute) => ["value" => "", "quote" => '']];
                    }
                    $this->next();
                } elseif ($this->html[$this->key] == ">") {
                    print_r("open is" . $this->html[$this->key] . "");
                    $this->activetag["status"] = "open";
                } else {
                    $attribute .= $this->html[$this->key];
                    $this->next();
                }
            }
        }
    }

    public function addstring(string $string)
    {
        if ($this->activetag) {
            $this->tagtostring($string);
        }
        array_push($this->tags, ["tag" => "", "string" => $string]);
    }

    private function tagtostring(string $addtionalstring = "")
    {
        print_r($this->activetag);
        if ($this->activetag) {
            $string = $this->activetag["tag"] && "";
            foreach ($this->activetag["attribute"] ?? [] as $value) {
                $string .= $value["quote"] ?? "" . $value["value"] ?? "" . $value["quote"] ?? "" . " ";
            }
            $this->activetag = null;
            $this->addstring($string . $addtionalstring);
        }
    }

    private function checktagisopen()
    {
        if ($this->html[$this->key] == "<") {
            if (preg_match("/[A-Za-z]/", $this->html[$this->key + 1])) {
                $this->tagtostring();
                return true;
            }
        }
        return false;
    }

    private function settag()
    {
        $this->activetag["tag"] = ($this->activetag["tag"] ?? "") . $this->html[$this->key];
        print_r("\n" . $this->activetag["tag"] . "\n");
    }

    private function next($any = null, $key = null)
    {
        print_r($any);
        $this->key++;
        if ($key) {
            $this->key = $key;
        }
        print_r($this->html[$this->key]);
    }

    private function closetag(bool $bool = false, string $print = null)
    {
        print_r($this->activetag);
        if ($print) {
            print_r($print);
        }
        if (in_array($this->activetag["tag"], $this->selfClosing) || $bool) {
            $this->tags[] = $this->activetag;
            $this->activetag = null;
        } else {
            $this->activetag["status"] = "open";
        }
    }
}
