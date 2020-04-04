<h1>
    <?= ml('Competitors.Competitors') ?>
</h1>
<table>
    <tr>
        <td>    
            <table class="table_info">    
                <tr>
                    <td>
                        <i class="fas fa-filter"></i>
                        <?= ml('Competitors.CitizenOf') ?>
                    </td>
                    <td>
                        <select data-competitors-request='country' data-selected="<?= $data->filter->country ?>">
                            <option value="">
                                <?= ml('Competitors.Select.All') ?>
                            </option>
                            <option disabled>
                                ------
                            </option>
                            <?php foreach ($data->counties as $country) { ?>
                                <option value="<?= $country->code ?>">        
                                    <?= $country->name ?>
                                </option> 
                            <?php } ?>      
                        </select>               
                    </td>
                </tr>   
                <tr>
                    <td>
                        <i class="fas fa-sort-amount-up"></i>
                        <?= ml('Competitors.Sort') ?>
                    </td>
                    <td>
                        <select data-competitors-request='sort' data-selected="<?= $data->sort ?>">
                            <option value="name">
                                <?= ml('Competitors.Competitor') ?>
                            </option>
                            <option value="competitions" >
                                <?= ml('Competitors.Competitions') ?>
                            </option>
                            <option value="events">
                                <?= ml('Competitors.Events') ?>
                            </option>
                            <option value="podiums">
                                <?= ml('Competitors.Podiums') ?>
                            </option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <i class="fas fa-search"></i> 
                        <?= ml('Competitors.Find') ?>
                    </td>
                    <td>
                        <input autocomplete="off" ID="competitor-find"/>
                    </td>
                </tr>
            </table>        
        </td>
    </tr>
</table>

<table class="table_new" data-competitors>
    <thead>
        <tr>
            <td>
                <?= ml('Competitors.Competitor') ?>
            </td>
            <td>
                <?= ml('Competitors.WCAID') ?>
            </td>
            <td></td>
            <td>
                <?= ml('Competitors.Country') ?>
            </td>
            <td>
                <?= ml('Competitors.Competitions') ?>
            </td>
            <td>
                <?= ml('Competitors.Events') ?>
            </td>
            <td>
                <?= ml('Competitors.Podiums') ?>
            </td>
        </tr> 
    </thead>
    <tbody>    
        <?php foreach ($data->competitors as $competitor) { ?>
            <tr data-key="<?= $competitor->name ?> <?= $competitor->wcaid ?>">
                <td>
                    <a href="<?= $competitor->link ?>">            
                        <?= $competitor->name ?> 
                    </a>
                </td>
                <td>
                    <?= $competitor->wcaid ?>
                </td>
                <td>
                    <?= $competitor->country->image ?>
                </td>
                <td>
                    <?= $competitor->country->name ?>
                </td>
                <td class="table_new_center">
                    <?= $competitor->competitions ?>
                </td>
                <td class="table_new_center">
                    <?= $competitor->events ?>
                </td>
                <td class="table_new_center">
                    <?= $competitor->podiums ?>
                </td>
            </tr>    
        <?php } ?>
    </tbody>
</table>
