<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 5/19/18
 * Time: 8:18 PM
 */

class Struct
{


    public function exec()
    {
        $link_list = new SingelLinkList();
        $link_list->InsertElem(new Node(1,'1211'));
        $link_list->InsertElem(new Node(2,'1211'));
        $link_list->InsertElem(new Node(3,'1211'));

        print_r($link_list);
    }

}


class Node
{
    public $id;
    public $name;
    public $next_pointer;

    public function __construct(int $id = 0, string $name = '')
    {
        $this->id = $id;
        $this->name = $name;
        $this->next_pointer = null;
    }

}

class SingelLinkList
{
    public $header = null;
    const SUCCESS = 1;
    const ERROR = 0;

    public function __construct()
    {
        $this->header = new Node();
    }

    public function InsertElem(Node $node) : int
    {
        $current = $this->header;
        while ($current->next_pointer != null){
            if($current->next_pointer->id > $node->id){
                break;
            }
            $current = $current->next_pointer;
        }

        $node->next_pointer = $current->next_pointer;
        $current->next_pointer = $node;

        return self::SUCCESS;
    }


}

$struct = new Struct();
$struct->exec();