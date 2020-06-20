<?php

function branch_region($id = 0)
{
    $branchRegion = 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z';
    $arr = array();
    foreach (explode(',', $branchRegion) as $k => $v) {
        $arr[$k + 1] = $v;
    }

    return ($id > 0 ? $arr[$id] : $arr);
}