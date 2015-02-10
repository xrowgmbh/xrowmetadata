<?php

class xrowMetaDataOperator
{

    function operatorList()
    {
        return array(
            'metadata'
        );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array(
            'metadata' => array(
                'node_id' => array(
                    'type' => 'int' ,
                    'required' => true ,
                    'default' => null
                )
            )
        );
    }

    function getPlaceholders($str)
    {
        $result = array();
        preg_match_all('/(?<=\[)([^\]]*?)(?=\])/',$str, $result);
        return $result[0];
    }

    function modify( $tpl, $operatorName, $operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters )
    {
        switch ( $operatorName )
        {
            case 'metadata':
            {
                if( isset( $namedParameters['node_id'] ) )
                {
                    $cur_node = eZContentObjectTreeNode::fetch( $namedParameters['node_id'] );
                    if( $cur_node instanceof eZContentObjectTreeNode )
                    {
                        $cur_parent=$cur_node->fetchParent();
                        $obj_name=$cur_node->getName();
                        $obj_count =$cur_node->subTreeCount(array( 'IgnoreVisibility' => true ));
                        $path_array_temp=array_reverse(explode('/',$cur_node->pathWithNames()));
                        $obj_path =implode(' | ',$path_array_temp);
                        $obj_parentname =$cur_parent->Name;
                        
                        $operatorValue = xrowMetaDataFunctions::fetchByObject( $cur_node->attribute( 'object' ) );
                        
                        $search_title=$operatorValue->title;
                        $search_description = $operatorValue->description;

                        $placeholder_title_array=self::getPlaceholders($search_title);
                        $placeholder_title_description=self::getPlaceholders($search_description);
                         
                        if(count($placeholder_title_array) !== 0)
                        {
                            foreach($placeholder_title_array as $placeholder_title)
                            {
                                switch($placeholder_title)
                                {
                                    case "count":
                                        $meta_title=str_replace("[count]",$obj_count,$search_title);
                                        continue;
                                    case "name":
                                        $meta_title=str_replace("[name]",$obj_name,$search_title);
                                        continue;
                                    case "path":
                                        $meta_title=str_replace("[path]",$obj_path,$search_title);
                                        continue;
                                    case "parentname":
                                        $meta_title=str_replace("[parentname]",$obj_parentname,$search_title);
                                        continue;
                                }
                                $search_title = $meta_title;
                            }
                        }
                         
                        if(count($placeholder_title_description) !== 0)
                        {
                            foreach($placeholder_title_description as $placeholder_description)
                            {
                                switch($placeholder_description)
                                {
                                    case "count":
                                        $meta_description=str_replace("[count]",$obj_count,$search_description);
                                        continue;
                                    case "name":
                                        $meta_description=str_replace("[name]",$obj_name,$search_description);
                                        continue;
                                    case "path":
                                        $meta_description=str_replace("[path]",$obj_path,$search_description);
                                        continue;
                                    case "parentname":
                                        $meta_description=str_replace("[parentname]",$obj_parentname,$search_description);
                                        continue;
                                }
                                $search_description = $meta_description;
                            }
                        }
                        
                        $operatorValue->title=$search_title;
                        $operatorValue->description=$search_description;
                    }
                    else
                    {
                        $operatorValue = false;
                    }
                }
                else
                {
                    $operatorValue = false;
                }
            }break;
        }
        unset($search_title,$search_description);
    }
}

?>