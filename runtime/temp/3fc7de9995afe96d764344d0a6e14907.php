<?php /*a:1:{s:87:"/www/wwwroot/tlsj/application/wap/config/../view/default/product/user_product_list.html";i:1577103174;}*/ ?>
<?php foreach($userProductList as $k => $v): ?>
<div class="mrythudHe mrythudHe<?php echo htmlentities($v['position']); ?>" data-id="<?php echo htmlentities($v['product_id']); ?>" data-infoid="<?php echo htmlentities($v['id']); ?>">
    <div class="mrythud">
        <img class="img" src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_<?php echo htmlentities($v['product_id']); ?>.png" alt="">
         <?php if($v['product_id'] >= 38): ?> 
            <img class="img img_word" src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_<?php echo htmlentities($v['product_id']); ?>_logo.png" alt="">
        <?php endif; ?>
        <!-- <img class="img" src="/template/wap/default/Static/centImages/v_<?php echo htmlentities($v['product_id']); ?>.png" alt=""> -->
        <div class="mrythdj"><?php echo htmlentities($v['product_number']); ?></div>
        <div class="maopap maopap_1" data-income="<?php echo htmlentities($v['income']); ?>"><?php echo htmlentities($v['show_income']); ?></div>
    </div>
</div>
<?php endforeach; ?>