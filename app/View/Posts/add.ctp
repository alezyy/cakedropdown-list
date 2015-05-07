<?php
$this->Js->get('#PostCategoryId')->event('change', 
    $this->Js->request(array(
        'controller'=>'subcategories',
        'action'=>'getByCategory'
        ), array(
        'update'=>'#PostSubcategoryId',
        'async' => true,
        'method' => 'post',
        'dataExpression'=>true,
        'data'=> $this->Js->serializeForm(array(
            'isForm' => true,
            'inline' => true
            ))
        ))
    );
?>

<div class="posts form">
<?php echo $this->Form->create('Post'); ?>
	<fieldset>
		<legend><?php echo __('Add Post'); ?></legend>
	<?php
		
    echo $this->Form->input('title');
    echo $this->Form->input('category_id', array('empty' => '-Pick Category-'));
    echo $this->Form->input('subcategory_id', array('empty' => '-No Category Selected-'));
	        
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Posts'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Subcategories'), array('controller' => 'subcategories', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Subcategory'), array('controller' => 'subcategories', 'action' => 'add')); ?> </li>
	</ul>
</div>
