<h1>
    Texts / Settings
</h1>
<?php  $request=getRequest();
$block_id=false;
if(isset($request[1])){
    $block_id=$request[1];
} ?>
<table width='100%'><tr><td  width='10%'>

<table class='table_new'>
    <thead>
    <tr>
       <td/>
       <td>ID</td> 
       <td>Block</td> 
       <td>Country</td> 
    </tr>
    </thead>
    <tbody>
        <?php 
        $blocks=[];
        DataBaseClass::FromTable("BlockText"); 
        DataBaseClass::OrderClear("BlockText","Name");
        DataBaseClass::Order_current("Country");
        foreach(DataBaseClass::QueryGenerate() as $block)if($block['BlockText_Name']!='MainRegulation'){ 
            $blocks[$block['BlockText_ID']]=$block; ?>
        <tr>
            <td class="<?= $block_id==$block['BlockText_ID']?'list_select':''?>"></td>
            <td><?= $block['BlockText_ID'] ?></td>
            <td><a class="<?= $block_id==$block['BlockText_ID']?'select':''?>" href="<?= PageIndex()?>Texts/<?= $block['BlockText_ID'] ?>"><?= $block['BlockText_Name'] ?></a><span>
            </td>
            <td><?= ImageCountry($block['BlockText_Country'])?> <?= CountryName($block['BlockText_Country'],true) ?></td>
         </tr>   
        <?php } ?>
    </tbody>
</table>
            
</td><td width='90%'>
    <?php if($block_id){ 
        $block=$blocks[$block_id]; ?>
    <h3><?= $block['BlockText_Name'] ?> <?= ImageCountry($block['BlockText_Country'])?> <?= CountryName($block['BlockText_Country'],true) ?></h3>
            <?= Parsedown($block['BlockText_Value'] ); ?><br>
            <form method="POST" action="<?= PageAction('Texts.Edit') ?>">
                <input name="Country" type="hidden" value="<?= $block['BlockText_Country'] ?>">
                <input name="Name" type="hidden" value="<?= $block['BlockText_Name'] ?>">
                <textarea name="Comment" style="height: 200px;width: 600px"><?= $block['BlockText_Value'] ?></textarea><br>
                <button><i class="fas fa-save"></i> Save</button>
            </form>
    <?php } ?>    
</td><tr></table>   
</div>    