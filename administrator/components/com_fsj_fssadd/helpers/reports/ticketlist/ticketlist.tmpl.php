
  <title><?php echo $this->report->title; ?></title>
  <description><?php echo $this->report->description; ?></description>


  <object>
	<![CDATA[
		<?php print_p($this); ?>
	]]>
  </object>

  <sql>
    <![CDATA[
    SELECT

    IF (
      t.admin_id = 0,
      'Unassigned',
      IF (au.name IS NULL,
        'Unknown',
        CONCAT(au.name, ' (', au.username, ')')
      )
    ) as handler,

    IF (tu.name IS NULL,
      IF (
        t.unregname = '',
        'Unknown',
        CONCAT(t.unregname, ' (-- unreg --)')
      ),
      CONCAT(tu.name, ' (', tu.username, ')')
    ) as user,

    tu.username as user_username,
    tu.name as user_name,
    tu.email as user_email,

    au.username as handler_username,
    au.name as handler_name,
    au.email as handler_email,

    t.*,

	<?php
	require_once (JPATH_SITE.DS.'components'.DS.'com_fss'.DS.'helper'.DS.'translate.php');
	require_once (JPATH_SITE.DS.'components'.DS.'com_fss'.DS.'helper'.DS.'fields.php');
	$cust_fields = FSSCF::GetAllCustomFields();

	foreach ($cust_fields as $cust_field)
	{
		$id = $cust_field['id'];
		$fname = "custom{$id}";
		if ($this->fielddata->$fname->row < 1) continue;

		echo "cf{$id}.value as custom{$id}, \n";
	} 
	?>

    s.title as status,
    s.translation as status_translation,
    c.title as category,
    c.translation as category_translation,
    p.title as product,
    p.translation as product_translation,
    d.title as department,
    d.translation as department_translation,
    pri.title as priority,
    pri.translation as priority_translation,
    concat(floor(timetaken / 60),':',lpad(mod(timetaken, 60), 2, '0')) as time
       
    FROM #__fss_ticket_ticket as t


    LEFT JOIN #__users as au ON t.admin_id = au.id

    LEFT JOIN #__users as tu ON t.user_id = tu.id

    LEFT JOIN #__fss_ticket_status as s ON t.ticket_status_id = s.id

    LEFT JOIN #__fss_ticket_cat as c ON t.ticket_cat_id = c.id
    LEFT JOIN #__fss_ticket_dept as d ON t.ticket_dept_id = d.id
    LEFT JOIN #__fss_prod as p ON t.prod_id = p.id
    LEFT JOIN #__fss_ticket_pri as pri ON t.ticket_pri_id = pri.id

	<?php foreach ($cust_fields as $cust_field)
	{
		$id = $cust_field['id'];
		$fname = "custom{$id}";
		if ($this->fielddata->$fname->row < 1) continue;

		if ($cust_field['peruser']) {
			echo "LEFT JOIN #__fss_ticket_user_field as cf{$id} ON t.user_id = cf{$id}.user_id AND cf{$id}.field_id = {$id}\n";
		} else {
			echo "LEFT JOIN #__fss_ticket_field as cf{$id} ON t.id = cf{$id}.ticket_id AND cf{$id}.field_id = {$id}\n";
		}
	} 
	?>

    WHERE 1
    
<?php if (isset($this->filterdata->status) && $this->filterdata->status): ?>
    {if,status,"all_open"}
        AND
               ticket_status_id IN (SELECT id FROM  #__fss_ticket_status as s WHERE is_closed = 0)
    {endif}
    
    {if,status,"all_open",not}
      {if,status,"all",not}
        {if,status}
          AND
              ticket_status_id = '{status}'
        {endif}
      {endif}
    {endif}
<?php endif; ?>
    
<?php if (isset($this->filterdata->product) && $this->filterdata->product): ?>
    {if,product}
        AND
            {product}
    {endif}
<?php endif; ?>
    
<?php if (isset($this->filterdata->department) && $this->filterdata->department): ?>
    {if,department}
        AND
            {department}
    {endif}
<?php endif; ?>

<?php if (isset($this->filterdata->user) && $this->filterdata->user): ?>
    {if,user}
        AND
            t.user_id = '{user}'
    {endif}
<?php endif; ?>
      
<?php if (isset($this->filterdata->opened) && $this->filterdata->opened != ""): ?>
			   AND
      <?php echo $this->filterdata->opened; ?> BETWEEN '{<?php echo $this->filterdata->opened; ?>_from} 00:00:00' AND '{<?php echo $this->filterdata->opened; ?>_to} 23:59:59'
<?php endif; ?>
    
<?php if (isset($this->filterdata->handler) && $this->filterdata->handler): ?>
    {if,handler,"",not}
        AND
            admin_id = '{handler}'
    {endif}
<?php endif; ?>
     
<?php if (isset($this->filterdata->group) && $this->filterdata->group): ?>
    {if,group}
        AND
            t.user_id IN (SELECT user_id FROM #__fss_ticket_group_members WHERE group_id = '{group}')
    {endif}
<?php endif; ?>
     
    AND t.source != 'email' AND t.source != 'email_declined'
    
    ORDER BY opened DESC
]]>
  </sql>

	<?php 
	foreach ($this->fielddata as $id => $attribs)
	{
		if ($attribs->row < 1) continue;
		$attribs->name = $id;

		$this->addLinks($attribs);

		if (isset($attribs->nowrap) && $attribs->nowrap) $attribs->style .= ";white-space: nowrap;";
		$label = $attribs->label;
		unset($attribs->label);
		echo $this->makeField($label, $attribs) . "\n";
	}
	?>

<?php if (isset($this->filterdata->opened) && $this->filterdata->opened != ""): ?>
	<filter>
		<name><?php echo $this->filterdata->opened; ?></name>
		<type>daterange</type>
		<field><?php echo $this->filterdata->opened; ?></field>
	</filter>
<?php endif; ?>

<?php if (isset($this->filterdata->status) && $this->filterdata->status): ?>
  <filter>
    <name>status</name>
    <type>normal</type>
    <sql>SELECT * FROM #__fss_ticket_status</sql>
    <key>id</key>
    <display>title</display>
    <translate>1</translate>

    <default>all_open</default>

    <extra key="all" value="all">ALL_TICKETS</extra>
    <extra key="all_open" value="all_open">ALL_OPEN</extra>

    <title>STATUS</title>
  </filter>
<?php endif; ?>

<?php if (isset($this->filterdata->product) && $this->filterdata->product): ?>
  <filter>
    <name>product</name>
    <type>lookup</type>
    <table>#__fss_prod</table>
    <field>prod_id</field>
    <translate>1</translate>
    <key>id</key>
    <display>title</display>
    <order>ordering</order>
    <header>SELECT_PRODUCT</header>
    <title>PRODUCT</title>
    <published>1</published>
  </filter>
<?php endif; ?>

<?php if (isset($this->filterdata->department) && $this->filterdata->department): ?>
  <filter>
    <name>department</name>
    <type>lookup</type>
    <table>#__fss_ticket_dept</table>
    <translate>1</translate>
    <field>ticket_dept_id</field>
    <key>id</key>
    <display>title</display>
    <order>title</order>
    <title>DEPARTMENT</title>
    <header>SELECT_DEPARTMENT</header>
  </filter>
<?php endif; ?>

<?php if (isset($this->filterdata->handler) && $this->filterdata->handler): ?>
  <filter>
    <name>handler</name>
    <type>normal</type>
    <sql>SELECT f.user_id as id, CONCAT(u.name, ' (', u.username, ')') as title FROM #__fss_users as f LEFT JOIN #__users as u ON f.user_id = u.id ORDER BY u.name</sql>
    <key>id</key>
    <display>title</display>

    <default>all</default>

    <extra key="" value="">ALL_HANDLERS</extra>
    <extra key="unassigned" value="0">UNASSIGNED</extra>

    <title>HANDLER</title>
  </filter>
<?php endif; ?>

<?php if (isset($this->filterdata->user) && $this->filterdata->user): ?>
  <filter>
    <name>user</name>
    <type>normal</type>
    <sql>SELECT u.id, IF(u.id, CONCAT(u.name, ' (', u.username, ')'), 'Unknown') as title FROM #__fss_ticket_ticket as t LEFT JOIN #__users as u ON t.user_id = u.id WHERE t.user_id > 0 AND u.id > 0 GROUP BY u.id ORDER BY u.name</sql>
    <key>id</key>
    <display>title</display>

    <default>all</default>

    <extra key="" value="">ALL_USERS</extra>
    <extra key="0" value="0">UNREGISTERED</extra>

    <title>USER</title>
  </filter>
<?php endif; ?>

<?php if (isset($this->filterdata->group) && $this->filterdata->group): ?>
  <filter>
    <name>group</name>
    <type>normal</type>
    <sql>SELECT * FROM #__fss_ticket_group ORDER BY groupname</sql>
    <key>id</key>
    <display>groupname</display>

    <default></default>

    <extra key="" value="">SELECT_TICKET_GROUP</extra>

    <title>TICKET_GROUP</title>
  </filter>
<?php endif; ?>

  <translate>
    <field>status</field>
    <data>status_translation</data>
    <source>title</source>
  </translate>

  <translate>
    <field>category</field>
    <data>category_translation</data>
    <source>title</source>
  </translate>

  <translate>
    <field>product</field>
    <data>product_translation</data>
    <source>title</source>
  </translate>

  <translate>
    <field>department</field>
    <data>department_translation</data>
    <source>title</source>
  </translate>

  <translate>
    <field>priority</field>
    <data>priority_translation</data>
    <source>title</source>
  </translate>
