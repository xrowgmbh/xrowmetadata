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
        $ini= eZINI::instance('xrowmetadata.ini');
        $page_limit = $ini->variable( 'EditorInputSettings', 'MaxPageLimit' );
        $uri = eZURI::instance( eZSys::requestURI() );
        $viewParameters = $uri->UserParameters();
        if(count($viewParameters)==0)
        {
            $page_offset=0;
        }else{
            $page_offset=$viewParameters['offset'];
        }
        switch ( $operatorName )
        {
            case 'metadata':
            {
                if( isset( $namedParameters['node_id'] ) )
                {
                    $node = eZContentObjectTreeNode::fetch( $namedParameters['node_id'] );
                    if( $node instanceof eZContentObjectTreeNode )
                    {
                        $cur_parent = $node->fetchParent();
                        $obj_name = $node->getName();
                        $obj_count = $node->subTreeCount(array( 'IgnoreVisibility' => true ));
                        $localbusiness_count= $node->subTreeCount(array( 'IgnoreVisibility' => true,
                                                                         'ClassFilterType'=> 'include',
                                                                         'ClassFilterArray'=> array('localbusiness')));
                        $path_array_temp = array_reverse(explode('/', $node->pathWithNames()));
                        $obj_path = implode(' | ', $path_array_temp);
                        $obj_parentname = $cur_parent->Name;
                        $page_count=ceil($localbusiness_count/$page_limit);
                        $page_nr=($page_offset/$page_limit)+1;
                        $page_count_value=ezpI18n::tr( 'kernel/classes/datatypes', 'page' ). $page_nr."/".$page_count;
                        $operatorValue = xrowMetaDataFunctions::fetchByObject( $node->attribute( 'object' ) );
                        if($operatorValue !== false)
                        {
                            if(isset($operatorValue->title))
                            {
                                $search_title = $operatorValue->title;
                                $placeholder_title_array = self::getPlaceholders($search_title);
                                if(count($placeholder_title_array) !== 0)
                                {
                                    foreach($placeholder_title_array as $placeholder_title)
                                    {
                                        switch($placeholder_title)
                                        {
                                            case "count":
                                                $meta_title = str_replace("[count]", $obj_count, $search_title);
                                                continue;
                                            case "name":
                                                $meta_title = str_replace("[name]", $obj_name, $search_title);
                                                continue;
                                            case "path":
                                                $meta_title = str_replace("[path]", $obj_path, $search_title);
                                                continue;
                                            case "parentname":
                                                $meta_title = str_replace("[parentname]", $obj_parentname, $search_title);
                                                continue;
                                            case "count:localbusiness":
                                                $meta_title = str_replace("[count:localbusiness]", $localbusiness_count, $search_title);
                                                continue;
                                            case "pagecount":
                                                $meta_title = str_replace("[pagecount]", $page_count_value, $search_title);
                                                continue;
                                        }
                                        $search_title = $meta_title;
                                    }
                                    $operatorValue->title = $search_title;
                                    unset($search_title);
                                }
                            }
                            if(isset($operatorValue->description))
                            {
                                $search_description = $operatorValue->description;
                                $placeholder_title_description = self::getPlaceholders($search_description);
                                if(count($placeholder_title_description) !== 0)
                                {
                                    foreach($placeholder_title_description as $placeholder_description)
                                    {
                                        switch($placeholder_description)
                                        {
                                            case "count":
                                                $meta_description = str_replace("[count]", $obj_count, $search_description);
                                                continue;
                                            case "name":
                                                $meta_description = str_replace("[name]", $obj_name, $search_description);
                                                continue;
                                            case "path":
                                                $meta_description = str_replace("[path]", $obj_path, $search_description);
                                                continue;
                                            case "parentname":
                                                $meta_description = str_replace("[parentname]", $obj_parentname, $search_description);
                                                continue;
                                            case "count:localbusiness":
                                                $meta_description = str_replace("[count:localbusiness]", $localbusiness_count, $search_description);
                                                continue;
                                            case "pagecount":
                                                $meta_description = str_replace("[pagecount]", $page_count_value, $search_description);
                                                continue;
                                        }
                                        $search_description = $meta_description;
                                    }
                                    $operatorValue->description = $search_description;
                                    unset($search_description);
                                }
                            }
                        }
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
    }
}

?>