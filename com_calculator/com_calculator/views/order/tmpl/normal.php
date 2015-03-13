<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>

<?php if ($this->model->ordered) {?>
	<?php echo $this->model->order_message; ?>
	<a href="<?php echo JURI::current(); ?>"><h3>Оформить новый заказ</h3></a>
<?php } else { ?>
<h1>Расчет стоимости отправки</h1>
<form id="calculator" method="POST" name="calculate_form" action="">
	<div id="calculator_body">
		<div class="control-group">
			<label class="control-label">Вес, кг<span class="asterisk correct">*</span></label>
			<div class="controls">
				<input class="comma-replace advantage_fields <?php if($this->model->weight !== null && $this->model->weight == 0) echo 'alert-error'?>" type="text" name="weight" value="<?php echo $this->model->weight; ?>" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">Оценка, руб</label>
			<div class="controls">
				<input class="comma-replace advantage_fields" type="text" name="assessed_value" value="<?php echo $this->model->assessed_value; ?>" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">Ширина, см<span class="asterisk correct">*</span></label>
			<div class="controls">
				<input class="comma-replace advantage_fields <?php if($this->model->width !== null && $this->model->width == 0) echo 'alert-error'?>" type="text" name="width" value="<?php echo $this->model->width; ?>" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">Длина, см<span class="asterisk correct">*</span></label>
			<div class="controls">
				<input class="comma-replace advantage_fields <?php if($this->model->length !== null && $this->model->length == 0) echo 'alert-error'?>" type="text" name="length" value="<?php echo $this->model->length; ?>" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">Высота, см<span class="asterisk correct">*</span></label>
			<div class="controls">
				<input class="comma-replace advantage_fields <?php if($this->model->height !== null && $this->model->height == 0) echo 'alert-error'?>" type="text" name="height" value="<?php echo $this->model->height; ?>" />
			</div>
		</div>
		<div class="control-group radio-group">
			<label class="control-label">Тариф</label>
			<div class="controls">
				<label><input class="advantage_fields" type="radio" name="is_express" value="1" <?php if($this->model->is_express === null || $this->model->is_express == 1) echo "checked" ?> />
				<span>Экспресс</span></label>
				<b class="separate">|</b>
				<label><input class="advantage_fields" type="radio" name="is_express" value="0" <?php if($this->model->is_express == 0) echo "checked" ?> />
				<span>Стандарт</span></label>
			</div>
		</div>
		<div class="control-group border-top">
			<label class="control-label">Откуда<span class="asterisk correct">*</span></label>
			<div class="controls">
				<select id="city_from" name="city_from" class="city_select advantage_fields">
					<option value="">Нет</option>
					<?php
						foreach($this->cities as $city){
							$selected = $city->city == $this->model->city_from ? " selected=\"selected\" " : "";
							echo "<option ".$selected." value=\"".$city->city."\">".$city->name."</option>";
						}
					 ?>		
				</select>
			</div>
		</div>
       <div class="control-group radio-group from">
			<label class="control-label">Забрать</label>
			<div class="controls"><!-- по умолчанию доставка от окна -->
				<label><input type="radio" class="advantage_fields" name="from_door" value="1" <?php if(JRequest::getFloat('from_door', 0) == 1) echo "checked" ?> onchange="jQuery('.from_window').toggle(); jQuery('.from_door').toggle();" /><span>Забрать от адреса</span></label>
				<b class="separate">|</b>
				<label><input type="radio" class="advantage_fields" name="from_door" value="0" <?php if(JRequest::getFloat('from_door', 0) == 0) echo "checked" ?> onchange="jQuery('.from_window').toggle(); jQuery('.from_door').toggle();" /><span>Самостоятельно привезти</span></label>
        	</div>
        </div>
		<div class="control-group margin-bottom">
			<label class="control-label"></label>
			<div class="controls">
            	<!-- код забора груза от окна -->
				<div class="from_window terminal-select" <?php if(JRequest::getFloat('from_door', 0) == 1) echo "style=\"display:none;\"" ?>>
					<select name="from_terminal" id="city_from_terminal" class="<?php if($this->model->price != null && JRequest::getFloat('from_door', 0) == 0 && count($this->terminals["from"]) == 0) echo 'alert-error'; ?>">
						<?php foreach($this->terminals["from"] as $t){
								$selected = $t->terminal == $this->model->form['from_terminal'] ? " selected=\"selected\" " : "";
								echo "<option ".$selected." value=".$t->terminal." >".$t->name."</option>";
						} ?>
					</select>
				</div>
				<!-- код забора груза от двери -->
				<div class="from_door door-group" <?php if(JRequest::getFloat('from_door', 0) == 0) echo "style=\"display:none;\"" ?>>
					<input type="text" name="from_door_street" class="<?php if(JRequest::getFloat('from_door', 0) == 1 && array_key_exists('from_door_street', $this->model->form) && empty($this->model->form['from_door_street'])) echo 'alert-error'; ?>" placeholder="Улица" value="<?php if(array_key_exists('from_door_street', $this->model->form)) echo $this->model->form['from_door_street']; ?>">
					
					<div id="from_door_address_parts" class="address-parts">
						<input type="text" name="from_door_house" placeholder="Дом" class="house <?php if( JRequest::getFloat('from_door', 0) == 1 && array_key_exists('from_door_house', $this->model->form) && empty($this->model->form['from_door_house'])) echo 'alert-error'; ?>" value="<?php if(array_key_exists('from_door_house', $this->model->form)) echo $this->model->form['from_door_house']; ?>">
						<input type="text" name="from_door_building" placeholder="Корп." class="building" value="<?php if(array_key_exists('from_door_building', $this->model->form)) echo $this->model->form['from_door_building']; ?>">
						<input type="text" name="from_door_structure" placeholder="Стр."  class="structure" value="<?php if(array_key_exists('from_door_structure', $this->model->form)) echo $this->model->form['from_door_structure']; ?>">
						<input type="text" name="from_door_flat" placeholder="Кв/оф" class="flat" value="<?php if(array_key_exists('from_door_flat', $this->model->form)) echo $this->model->form['from_door_flat']; ?>">
					</div>
				</div>
			
				<div class="from_door worktime" <?php if(JRequest::getFloat('from_door', 0) == 0) echo "style=\"display:none;\"" ?>>
					<div>
						<input type="text" name="from_door_worktime_start" value="<?php if(array_key_exists('from_door_worktime_start', $this->model->form)) echo $this->model->form['from_door_worktime_start']; ?>" placeholder="__:__">
						<span>&nbsp;&nbsp;—&nbsp;&nbsp;</span>
						<input type="text" name="from_door_worktime_end" value="<?php if(array_key_exists('from_door_worktime_end', $this->model->form)) echo $this->model->form['from_door_worktime_end']; ?>" placeholder="__:__">
						<span>время забора</span>
					</div>
					<div>
						<input type="text" name="from_door_breaktime_start" value="<?php if(array_key_exists('from_door_breaktime_start', $this->model->form)) echo $this->model->form['from_door_breaktime_start']; ?>" placeholder="__:__">
						<span>&nbsp;&nbsp;—&nbsp;&nbsp;</span>
						<input type="text" name="from_door_breaktime_end" value="<?php if(array_key_exists('from_door_breaktime_end', $this->model->form)) echo $this->model->form['from_door_breaktime_end']; ?>" placeholder="__:__">
						<span>перерыв</span>
					</div>
					<div id="from_door_exact_time_group" class="exact_time_group">
						<label><input type="checkbox" name="from_door_exact_time" <?php if(array_key_exists('from_door_exact_time', $this->model->form) && $this->model->form['from_door_exact_time'] == 1) echo 'checked'; ?> value="1" /> Фиксированное время забора</label>
					</div>
				</div>    
			</div>
		</div>
		<div class="control-group border-top">
			<label class="control-label">Куда<span class="asterisk correct">*</span></label>
			<div class="controls">
				<select id="city_to" name="city_to" class="city_select advantage_fields">
					<option value="">Нет</option>
					<?php
						foreach($this->cities as $city){
							$selected = $city->city == $this->model->city_to ? " selected=\"selected\" " : "";
							echo "<option ".$selected." value=\"".$city->city."\">".$city->name."</option>";
						}
					 ?>
				</select>
			</div>
		</div>
     	<div class="control-group radio-group to">
			<label class="control-label">Доставить</label>
			<div class="controls"><!-- по умолчанию доставка до двери -->
				<label><input type="radio" class="advantage_fields" name="to_door" value="1" <?php if(JRequest::getFloat('to_door', 1) == 1) echo "checked" ?> onchange="jQuery('.to_door').toggle(); jQuery('.to_window').toggle()" /><span>Доставить на адрес</span></label>
				<b class="separate">|</b>
				<label><input type="radio" class="advantage_fields" name="to_door" value="0" <?php if(JRequest::getFloat('to_door', 1) == 0) echo "checked" ?> onchange="jQuery('.to_door').toggle(); jQuery('.to_window').toggle()" /><span>Самостоятельно забрать</span></label>
            </div>
        </div>
    	<div class="control-group">
			<label class="control-label"></label>
			<div class="controls">
				<!-- код забора груза от окна -->
				<div class="to_window terminal-select" <?php if(JRequest::getFloat('to_door', 1) == 1) echo "style=\"display:none;\"" ?>>
					<select name="to_terminal" id="city_to_terminal" class="<?php if($this->model->price != null && JRequest::getFloat('to_door', 1) == 0 && count($this->terminals["to"]) == 0) echo 'alert-error'; ?>">
						<?php foreach($this->terminals["to"] as $t){
								$selected = $t->terminal == $this->model->form['to_terminal'] ? " selected=\"selected\" " : "";
								echo "<option ".$selected." value=".$t->terminal." >".$t->name."</option>";
						} ?>
					</select>
				</div>
				<!-- код доставки груза до двери -->
				<div class="to_door door-group" <?php if(JRequest::getFloat('to_door', 1) == 0) echo "style=\"display:none;\"" ?>>
					<input type="text" name="to_door_street" class="<?php if(JRequest::getFloat('to_door', 1) == 1 && array_key_exists('to_door_street', $this->model->form) && empty($this->model->form['to_door_street'])) echo 'alert-error'; ?>" placeholder="Улица" value="<?php if(array_key_exists('to_door_street', $this->model->form)) echo $this->model->form['to_door_street']; ?>">
					<div id="to_door_address_parts" class="address-parts">
						<input type="text" name="to_door_house" placeholder="Дом" class="house <?php if( JRequest::getFloat('to_door', 1) == 1 && array_key_exists('to_door_house', $this->model->form) && empty($this->model->form['to_door_house'])) echo 'alert-error'; ?>" value="<?php if(array_key_exists('to_door_house', $this->model->form)) echo $this->model->form['to_door_house']; ?>">
						<input type="text" name="to_door_building" placeholder="Корп." class="building" value="<?php if(array_key_exists('to_door_building', $this->model->form)) echo $this->model->form['to_door_building']; ?>">
						<input type="text" name="to_door_structure" placeholder="Стр."  class="structure" value="<?php if(array_key_exists('to_door_structure', $this->model->form)) echo $this->model->form['to_door_structure']; ?>">
						<input type="text" name="to_door_flat" placeholder="Кв/оф" class="flat" value="<?php if(array_key_exists('to_door_flat', $this->model->form)) echo $this->model->form['to_door_flat']; ?>">
					</div>
				</div>
   
				<div class="to_door worktime" <?php if(JRequest::getFloat('to_door', 1) == 0) echo "style=\"display:none;\"" ?>>
					<div>
						<input type="text" name="to_door_worktime_start" value="<?php if(array_key_exists('to_door_worktime_start', $this->model->form)) echo $this->model->form['to_door_worktime_start']; ?>" placeholder="__:__">
						<span>&nbsp;&nbsp;—&nbsp;&nbsp;</span>
						<input type="text" name="to_door_worktime_end" value="<?php if(array_key_exists('to_door_worktime_end', $this->model->form)) echo $this->model->form['to_door_worktime_end']; ?>" placeholder="__:__">
						<span>время забора</span>
					</div>
					<div>
						<input type="text" name="to_door_breaktime_start" value="<?php if(array_key_exists('to_door_breaktime_start', $this->model->form)) echo $this->model->form['to_door_breaktime_start']; ?>" placeholder="__:__">
						<span>&nbsp;&nbsp;—&nbsp;&nbsp;</span>
						<input type="text" name="to_door_breaktime_end" value="<?php if(array_key_exists('to_door_breaktime_end', $this->model->form)) echo $this->model->form['to_door_breaktime_end']; ?>" placeholder="__:__">
						<span>перерыв</span>
					</div>
					<div id="to_door_exact_time_group" class="exact_time_group">
						<label><input type="checkbox" name="to_door_exact_time" <?php if(array_key_exists('to_door_exact_time', $this->model->form) && $this->model->form['to_door_exact_time'] == 1) echo 'checked'; ?> value="1" /> Фиксированное время забора</label>
					</div>
				</div>
			</div>
		</div>
		<div class="control-group" id="calculated" style="<?php if($this->model->price == null){ echo "display:none;"; }?>">
			<div>
				<h2>Стоимость отправки: 
					<span id="price"><?php echo $this->model->price; ?></span> руб 
					<span style="text-transform:none;">(в том числе НДС <span id="nds_part"><?php echo $this->model->nds_part; ?></span> руб.)</span>
				</h2>
				Время доставки: 
				<span id="min_delivery_time"><?php echo $this->model->min_delivery_time; ?></span> &mdash; 
				<span id="max_delivery_time"><?php echo $this->model->max_delivery_time; ?></span> дн.
				Объем груза: 
				<span id="volume"><?php echo ($this->model->volume < 0.01 ? "менее 0,01" : $this->model->volume); ?></span> м<sup>3</sup>
			</div>
		</div>
		<div class="control-group" id="calculated_inner" style="<?php if($this->model->inner_price == null){ echo "display:none;"; }?>">
			<div>
				<h2>Внутренняя стоимость отправки: 
					<span id="inner_price"><?php echo $this->model->inner_price; ?></span> руб 
					<span style="text-transform:none;">(в том числе НДС <span id="nds_part_inner"><?php echo $this->model->nds_part_inner; ?></span> руб.)</span>
				</h2>
				<h2>Прибыль: 
					<span id="profit"><?php echo $this->model->profit; ?></span> руб 
					<span style="text-transform:none;">(в том числе НДС <span id="profit_nds_part"><?php echo $this->model->profit_nds_part; ?></span> руб.)</span>
				</h2>
			</div>
		</div>
		<div class="control-group">
			<div>
				<a href="#" id="order_details_link" style="display:none;" onclick="jQuery('#order_form').show(); jQuery('#order_details_link').hide(); return false;" >Оформить заказ</a>
			</div>
		</div>
	</div>

	<div id="order_form" style="<?php if($this->model->price == null){ echo "display:none;"; }?>">
		<div id="produce_date" class="form-block">
			<label for="produceDate" class="control-label">
				<span>Дата выполнения заявки</span><span class="asterisk correct">*</span>
			</label>
			<div class="controls">
				<?php
					echo JHTML::calendar((array_key_exists('produceDate', $this->model->form) ? $this->model->form['produceDate'] : ''),'produceDate', 'produceDate', '%Y-%m-%d', array('size'=>'8','maxlength'=>'10',
					'class'=>((array_key_exists('produceDate', $this->model->form) && empty($this->model->form['produceDate'])) ? 'alert-error' : '')));
				?>
				<span class="toDoor">дата забора груза</span> /
				<span class="terminal">дата передачи груза на терминал</span>
			</div>
		</div>

		<div id="comments" class="form-block">
			<label for="comments" class="control-label">Комментарии:</label>
			<div class="controls">
				<textarea name="comments"><?php if(array_key_exists('comments', $this->model->form)) echo $this->model->form['comments']; ?></textarea>
			</div>
		</div>

		<div id="counteragents">
			
			<div id="sender" class="form-block <?php if (array_key_exists('sender_legal_type', $this->model->form) && $this->model->form['sender_legal_type'] == 'physical') echo "physical"; ?>">
				<div class="control-group">
					<label class="control-label">Отправитель</label>
					<div class="controls">
						<label for="sender_legal_type" class="checkbox"><input type="checkbox" name="sender_legal_type" <?php if (array_key_exists('sender_legal_type', $this->model->form) && $this->model->form['sender_legal_type'] == 'physical') echo "checked"; ?> value="physical" id="sender_legal_type" onchange="UseFunctionSender(this.checked);"> физическое лицо</label>
					</div>
				</div>
        
				<div class="control-group juridical">
					<label for="sender_inn" class="control-label">ИНН<span class="asterisk">*</span></label>
					<div class="controls">
						<input type="text" name="sender_inn" class="<?php if (!array_key_exists('sender_legal_type', $this->model->form) && array_key_exists('sender_inn', $this->model->form) && empty($this->model->form["sender_inn"])) echo "alert-error"; ?>" id="sender_inn" value="<?php if(array_key_exists('sender_inn', $this->model->form)) echo $this->model->form['sender_inn']; ?>" />
					</div>
				</div>        
     
				<div class="control-group juridical">
					<label for="sender_company_name" class="control-label">Наименование<span class="asterisk">*</span></label>
					<div class="controls">
						<input type="text" name="sender_company_name" class="<?php if (!array_key_exists('sender_legal_type', $this->model->form) && array_key_exists('sender_company_name', $this->model->form) && empty($this->model->form["sender_company_name"])) echo "alert-error"; ?>" id="sender_company_name" value="<?php if(array_key_exists('sender_company_name', $this->model->form)) echo $this->model->form['sender_company_name']; ?>" />
					</div>
				</div>
        
				<div class="control-group physical">
					<label for="sender_name" class="control-label">ФИО<span class="asterisk">*</span></label>
					<div class="controls">
						<input type="text" name="sender_name" class="<?php if (array_key_exists('sender_legal_type', $this->model->form) && $this->model->form['sender_legal_type'] == "physical" && empty($this->model->form["sender_name"])) echo "alert-error"; ?>" value="<?php if(array_key_exists('sender_name', $this->model->form)) echo $this->model->form['sender_name']; ?>" id="sender_name">
						<span class="help-inline comment">Фамилия Имя Отчество<br> полностью</span>
					</div>
				</div>
        
				<div class="control-group juridical">
					<label for="sender_juridical_address" class="control-label">Юридический адрес<span class="asterisk">*</span></label>
					<div class="controls">
						<div id="sender_juridical_address">
							<input type="text" name="sender_ZIP_code" placeholder="Индекс" id="sender_ZIP_code" class="ZIP_code <?php if (!array_key_exists('sender_legal_type', $this->model->form) && array_key_exists('sender_ZIP_code', $this->model->form) && empty($this->model->form["sender_ZIP_code"])) echo "alert-error"; ?>" value="<?php if(array_key_exists('sender_ZIP_code', $this->model->form)) echo $this->model->form['sender_ZIP_code']; ?>">
							<input type="text" name="sender_juridical_city" placeholder="Населенный пункт" id="sender_juridical_city" class="juridical_city <?php if (!array_key_exists('sender_legal_type', $this->model->form) && array_key_exists('sender_juridical_city', $this->model->form) && empty($this->model->form["sender_juridical_city"])) echo "alert-error"; ?>" value="<?php if(array_key_exists('sender_juridical_city', $this->model->form)) echo $this->model->form['sender_juridical_city']; ?>">
							<input type="text" name="sender_juridical_street" placeholder="Улица" id="sender_juridical_street" class=" <?php if (!array_key_exists('sender_legal_type', $this->model->form) && array_key_exists('sender_juridical_street', $this->model->form) && empty($this->model->form["sender_juridical_street"])) echo "alert-error"; ?>" value="<?php if(array_key_exists('sender_juridical_street', $this->model->form)) echo $this->model->form['sender_juridical_street']; ?>">
							<input type="text" name="sender_house" placeholder="Дом" id="sender_house" class="house <?php if (!array_key_exists('sender_legal_type', $this->model->form) && array_key_exists('sender_house', $this->model->form) && empty($this->model->form["sender_house"])) echo "alert-error"; ?>" value="<?php if(array_key_exists('sender_house', $this->model->form)) echo $this->model->form['sender_house']; ?>">
							<input type="text" name="sender_building" placeholder="Корп." id="sender_building" class="building" value="<?php if(array_key_exists('sender_building', $this->model->form)) echo $this->model->form['sender_building']; ?>">
							<input type="text" name="sender_structure" placeholder="Стр." id="sender_structure" class="structure" value="<?php if(array_key_exists('sender_structure', $this->model->form)) echo $this->model->form['sender_structure']; ?>">
							<input type="text" name="sender_flat" placeholder="Кв/оф" id="sender_flat" class="flat" value="<?php if(array_key_exists('sender_flat', $this->model->form)) echo $this->model->form['sender_flat']; ?>">
						</div>
					</div>
				</div>
      
				<div class="control-group juridical">
					<label for="sender_contact" class="control-label">Контактное лицо<span class="asterisk">*</span></label>
					<div class="controls">
						<input type="text" name="sender_contact" class="<?php if (!array_key_exists('sender_legal_type', $this->model->form) && array_key_exists('sender_contact', $this->model->form) && empty($this->model->form["sender_contact"])) echo "alert-error"; ?>" value="<?php if(array_key_exists('sender_contact', $this->model->form)) echo $this->model->form['sender_contact']; ?>" id="sender_contact">
					</div>
				</div>
        
				<div class="control-group">
					<label for="sender_phone" class="control-label">Телефон<span class="asterisk">*</span></label>
					<div class="controls">
						<input type="text" name="sender_phone" class="<?php if ( array_key_exists('sender_phone', $this->model->form) && empty($this->model->form["sender_phone"])) echo "alert-error"; ?>" value="<?php if(array_key_exists('sender_phone', $this->model->form)) echo $this->model->form['sender_phone']; ?>" id="sender_phone">
					</div>
				</div>
			</div>
    
			<hr class="next-checkbox divider"/>
    
			<div id="receiver" class="form-block <?php if (array_key_exists('receiver_legal_type', $this->model->form) && $this->model->form['receiver_legal_type'] == 'physical') echo "physical"; ?>">
				<div class="control-group">
					<label class="control-label">Получатель</label>
					<div class="controls">
						<label for="receiver_legal_type" class="checkbox">
							<input type="checkbox" name="receiver_legal_type" <?php if (array_key_exists('receiver_legal_type', $this->model->form) && $this->model->form['receiver_legal_type'] == 'physical') echo "checked"; ?> value="physical" id="receiver_legal_type" onchange="UseFunctionReceiver(this.checked);"> физическое лицо
						</label>
					</div>
				</div>
	
				<div class="control-group juridical">
					<label for="receiver_inn" class="control-label">ИНН<span class="asterisk">*</span></label>
					<div class="controls">
					  <input type="text" name="receiver_inn" class="<?php if (!array_key_exists('receiver_legal_type', $this->model->form) && array_key_exists('receiver_inn', $this->model->form) && empty($this->model->form["receiver_inn"])) echo "alert-error"; ?>" id="receiver_inn" value="<?php if(array_key_exists('receiver_inn', $this->model->form)) echo $this->model->form['receiver_inn']; ?>" />
					</div>
				</div>       
     
				<div class="control-group juridical">
					<label for="receiver_company_name" class="control-label">Наименование<span class="asterisk">*</span></label>
					<div class="controls">
						<input type="text" name="receiver_company_name" class="<?php if (!array_key_exists('receiver_legal_type', $this->model->form) && array_key_exists('receiver_company_name', $this->model->form) && empty($this->model->form["receiver_company_name"])) echo "alert-error"; ?>" id="receiver_company_name" value="<?php if(array_key_exists('receiver_company_name', $this->model->form)) echo $this->model->form['receiver_company_name']; ?>" />
					</div>
				</div>
        
				<div class="control-group physical">
					<label for="receiver_name" class="control-label">ФИО<span class="asterisk">*</span></label>
					<div class="controls">
						<input type="text" name="receiver_name" class="<?php if (array_key_exists('receiver_legal_type', $this->model->form) && $this->model->form['receiver_legal_type'] == "physical" && empty($this->model->form["receiver_name"])) echo "alert-error"; ?>" value="<?php if(array_key_exists('receiver_name', $this->model->form)) echo $this->model->form['receiver_name']; ?>" id="receiver_name">
						<span class="help-inline comment">Фамилия Имя Отчество<br> полностью</span>
					</div>
				</div>
        
				<div class="control-group juridical">
					<label for="receiver_juridical_address" class="control-label">Юридический адрес<span class="asterisk">*</span></label>
					<div class="controls">
						<div id="receiver_juridical_address">
							<input type="text" name="receiver_ZIP_code" placeholder="Индекс" id="receiver_ZIP_code" class="ZIP_code <?php if (!array_key_exists('receiver_legal_type', $this->model->form) && array_key_exists('receiver_ZIP_code', $this->model->form) && empty($this->model->form["receiver_ZIP_code"])) echo "alert-error"; ?>" value="<?php if(array_key_exists('receiver_ZIP_code', $this->model->form)) echo $this->model->form['receiver_ZIP_code']; ?>">
							<input type="text" name="receiver_juridical_city" placeholder="Населенный пункт" id="receiver_juridical_city" class="juridical_city <?php if (!array_key_exists('receiver_legal_type', $this->model->form) && array_key_exists('receiver_juridical_city', $this->model->form) && empty($this->model->form["receiver_juridical_city"])) echo "alert-error"; ?>" value="<?php if(array_key_exists('receiver_juridical_city', $this->model->form)) echo $this->model->form['receiver_juridical_city']; ?>">
							<input type="text" name="receiver_juridical_street" placeholder="Улица" id="receiver_juridical_street" class=" <?php if (!array_key_exists('receiver_legal_type', $this->model->form) && array_key_exists('receiver_juridical_street', $this->model->form) && empty($this->model->form["receiver_juridical_street"])) echo "alert-error"; ?>" value="<?php if(array_key_exists('receiver_juridical_street', $this->model->form)) echo $this->model->form['receiver_juridical_street']; ?>">
							<input type="text" name="receiver_house" placeholder="Дом" id="receiver_house" class="house <?php if (!array_key_exists('receiver_legal_type', $this->model->form) && array_key_exists('receiver_house', $this->model->form) && empty($this->model->form["receiver_house"])) echo "alert-error"; ?>" value="<?php if(array_key_exists('receiver_house', $this->model->form)) echo $this->model->form['receiver_house']; ?>">
							<input type="text" name="receiver_building" placeholder="Корп." id="receiver_building" class="building" value="<?php if(array_key_exists('receiver_building', $this->model->form)) echo $this->model->form['receiver_building']; ?>">
							<input type="text" name="receiver_structure" placeholder="Стр." id="receiver_structure" class="structure" value="<?php if(array_key_exists('receiver_structure', $this->model->form)) echo $this->model->form['receiver_structure']; ?>">
							<input type="text" name="receiver_flat" placeholder="Кв/оф" id="receiver_flat" class="flat" value="<?php if(array_key_exists('receiver_flat', $this->model->form)) echo $this->model->form['receiver_flat']; ?>">
						</div>
					</div>
				</div>
      
				<div class="control-group juridical">
					<label for="receiver_contact" class="control-label">Контактное лицо<span class="asterisk">*</span></label>
					<div class="controls">
						<input type="text" name="receiver_contact" class="<?php if (!array_key_exists('receiver_legal_type', $this->model->form) && array_key_exists('receiver_contact', $this->model->form) && empty($this->model->form["receiver_contact"])) echo "alert-error"; ?>" value="<?php if(array_key_exists('receiver_contact', $this->model->form)) echo $this->model->form['receiver_contact']; ?>" id="receiver_contact">
					</div>
				</div>
        
				<div class="control-group">
					<label for="receiver_phone" class="control-label">Телефон<span class="asterisk">*</span></label>
					<div class="controls">
						<input type="text" name="receiver_phone" class="<?php if ( array_key_exists('receiver_phone', $this->model->form) && empty($this->model->form["receiver_phone"])) echo "alert-error"; ?>" value="<?php if(array_key_exists('receiver_phone', $this->model->form)) echo $this->model->form['receiver_phone']; ?>" id="receiver_phone">
					</div>
				</div>
			</div>

			<hr class="next-checkbox divider">

			<div id="payment" class="control-group">
				<label for="payment" class="radio-group-label control-label"><span>Выберите плательщика</span><span class="asterisk">*</span></label>
				<div class="controls">
					<div id="payment_truck" class="radio-group">
						<label class="radio">
							<input type="radio" name="payer" <?php if(array_key_exists('payer', $this->model->form) && $this->model->form['payer'] == 'sender') echo 'checked'; ?> value="sender" id="payer_sender" checked="checked" onchange="jQuery('#third').removeClass('payer')">Отправитель
						</label>
						<label class="radio">
							<input type="radio" name="payer" <?php if(array_key_exists('payer', $this->model->form) && $this->model->form['payer'] == 'receiver') echo 'checked'; ?> value="receiver" id="payer_receiver" onchange="jQuery('#third').removeClass('payer')">Получатель
						</label>
						<label class="radio juridical">
							<input type="radio" name="payer" <?php if(array_key_exists('payer', $this->model->form) && $this->model->form['payer'] == 'third') echo 'checked'; ?> value="third" id="payer_third" onchange="jQuery('#third').addClass('payer')">Третье лицо
						</label>
					</div>
				</div>
			</div>

			<hr class="divider">

			<div id="third" class="form-block juridical <?php if (array_key_exists('payer', $this->model->form) && $this->model->form['payer'] == 'third') echo "payer"; ?>">
				<div class="control-group">
					<label class="control-label">Третье лицо</label>
					<div class="controls">Только юридическое лицо</div>
				</div>

				<div class="control-group juridical">
					<label for="third_inn" class="control-label">ИНН<span class="asterisk">*</span></label>
					<div class="controls">
						<input type="text" name="third_inn" class="<?php if (array_key_exists('payer', $this->model->form) && $this->model->form['payer'] == 'third' && array_key_exists('third_inn', $this->model->form) && empty($this->model->form["third_inn"])) echo "alert-error"; ?>" id="third_inn" value="<?php if(array_key_exists('third_inn', $this->model->form)) echo $this->model->form['third_inn']; ?>" />
					</div>
				</div>
     
				<div class="control-group juridical">
					<label for="third_company_name" class="control-label">Наименование<span class="asterisk">*</span></label>
					<div class="controls">
						<input type="text" name="third_company_name" class="<?php if (array_key_exists('payer', $this->model->form) && $this->model->form['payer'] == 'third' && array_key_exists('third_company_name', $this->model->form) && empty($this->model->form["third_company_name"])) echo "alert-error"; ?>" id="third_company_name" value="<?php if(array_key_exists('third_company_name', $this->model->form)) echo $this->model->form['third_company_name']; ?>" />
					</div>
				</div>
        
				<div class="control-group juridical">
					<label for="third_juridical_address" class="control-label">Юридический адрес<span class="asterisk">*</span></label>
					<div class="controls">
						<div id="third_juridical_address">
							<input type="text" name="third_ZIP_code" placeholder="Индекс" id="third_ZIP_code" class="ZIP_code <?php if (array_key_exists('payer', $this->model->form) && $this->model->form['payer'] == 'third' && array_key_exists('third_ZIP_code', $this->model->form) && empty($this->model->form["third_ZIP_code"])) echo "alert-error"; ?>" value="<?php if(array_key_exists('third_ZIP_code', $this->model->form)) echo $this->model->form['third_ZIP_code']; ?>">
							<input type="text" name="third_juridical_city" placeholder="Населенный пункт" id="third_juridical_city" class="juridical_city <?php if (array_key_exists('payer', $this->model->form) && $this->model->form['payer'] == 'third' && array_key_exists('third_juridical_city', $this->model->form) && empty($this->model->form["third_juridical_city"])) echo "alert-error"; ?>" value="<?php if(array_key_exists('third_juridical_city', $this->model->form)) echo $this->model->form['third_juridical_city']; ?>">
							<input type="text" name="third_juridical_street" placeholder="Улица" id="third_juridical_street" class=" <?php if (array_key_exists('payer', $this->model->form) && $this->model->form['payer'] == 'third' && array_key_exists('third_juridical_street', $this->model->form) && empty($this->model->form["third_juridical_street"])) echo "alert-error"; ?>" value="<?php if(array_key_exists('third_juridical_street', $this->model->form)) echo $this->model->form['third_juridical_street']; ?>">
							<input type="text" name="third_house" placeholder="Дом" id="third_house" class="house <?php if (array_key_exists('payer', $this->model->form) && $this->model->form['payer'] == 'third' && array_key_exists('third_house', $this->model->form) && empty($this->model->form["third_house"])) echo "alert-error"; ?>" value="<?php if(array_key_exists('third_house', $this->model->form)) echo $this->model->form['third_house']; ?>">
							<input type="text" name="third_building" placeholder="Корп." id="third_building" class="building" value="<?php if(array_key_exists('third_building', $this->model->form)) echo $this->model->form['third_building']; ?>">
							<input type="text" name="third_structure" placeholder="Стр." id="third_structure" class="structure" value="<?php if(array_key_exists('third_structure', $this->model->form)) echo $this->model->form['third_structure']; ?>">
							<input type="text" name="third_flat" placeholder="Кв/оф" id="third_flat" class="flat" value="<?php if(array_key_exists('third_flat', $this->model->form)) echo $this->model->form['third_flat']; ?>">
						</div>
					</div>
				</div>
      
				<div class="control-group juridical">
					<label for="third_contact" class="control-label">Контактное лицо<span class="asterisk">*</span></label>
					<div class="controls">
						<input type="text" name="third_contact" class="<?php if (array_key_exists('payer', $this->model->form) && $this->model->form['payer'] == 'third' && array_key_exists('third_contact', $this->model->form) && empty($this->model->form["third_contact"])) echo "alert-error"; ?>" value="<?php if(array_key_exists('third_contact', $this->model->form)) echo $this->model->form['third_contact']; ?>" id="third_contact">
					</div>
				</div>
        
				<div class="control-group">
					<label for="third_phone" class="control-label">Телефон<span class="asterisk">*</span></label>
					<div class="controls">
						<input type="text" name="third_phone" class="<?php if (array_key_exists('payer', $this->model->form) && $this->model->form['payer'] == 'third' && array_key_exists('third_phone', $this->model->form) && empty($this->model->form["third_phone"])) echo "alert-error"; ?>" value="<?php if(array_key_exists('third_phone', $this->model->form)) echo $this->model->form['third_phone']; ?>" id="third_phone">
					</div>
				</div>
			</div>

			<hr class="divider">
			<input class="submit" type="submit" name="submit" value="Оформить заказ" />
		</div>
	</div>	
</form>
<?php }?>
