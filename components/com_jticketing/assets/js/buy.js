function displaycoupon()
{
	techjoomla.jQuery("#dis_amt").show();
	var total_amt_inputbox = parseFloat(techjoomla.jQuery("#total_amt_inputbox").val());
	total_amt_inputbox = total_amt_inputbox.toFixed(2)
	techjoomla.jQuery("#net_amt_pay_inputbox").val(total_amt_inputbox);
	techjoomla.jQuery("#net_amt_pay").html(total_amt_inputbox);

	if(techjoomla.jQuery("#coupon_chk").is(":checked"))
	{
		total_calc_amt2=0;

		techjoomla.jQuery("input[class='input-small type_ticketcounts']").each(function()
		{
			total_calc_amt2=parseFloat(total_calc_amt2)+parseFloat(techjoomla.jQuery(this).val())
		});

		if(total_calc_amt2==0)
		{
			alert(Joomla.JText._('COM_JTICKETING_NUMBER_OF_TICKETS'));
			document.getElementById("coupon_chk").checked = false;
			return;
		}

		document.getElementById("coup_button").removeAttribute("disabled");
		techjoomla.jQuery("#cop_tr").show();
		techjoomla.jQuery("#coupon_code").show();
		techjoomla.jQuery("#coup_button").show();
	}
	else
	{
		var totalamt=parseFloat(techjoomla.jQuery("#net_amt_pay_inputbox").val());
		totalamt = totalamt.toFixed(2);

		var allow_taxation=techjoomla.jQuery("#allow_taxation").val();
			if(allow_taxation==1)
			{
				calculatetax(totalamt);

			}

		techjoomla.jQuery("#cop_tr").hide();
		techjoomla.jQuery("#coupon_code").hide();
		techjoomla.jQuery("#coup_button").hide();
		techjoomla.jQuery("#dis_amt").show();
		techjoomla.jQuery("#dis_cop_amt").html();
		techjoomla.jQuery("#dis_cop").hide();
		techjoomla.jQuery("#coupon_code").val("");
	}
}

function applycoupon()
{
	document.getElementById("coup_button").setAttribute("disabled", "disabled");

	if(techjoomla.jQuery("#coupon_chk").is(":checked"))
	{
		if(techjoomla.jQuery("#coupon_code").val()=="")
		{
			document.getElementById("coup_button").removeAttribute("disabled");
			alert(Joomla.JText._('ENTER_COP_COD'));
		}
		else
		{
			techjoomla.jQuery.ajax(
			{
				url: jtBaseUrl+"index.php?option=com_jticketing&task=buy.getcoupon&coupon_code="+document.getElementById("coupon_code").value,
				type: "GET",
				dataType: "json",
				success: function(data)
				{
					amt=0;
					val=0;
					var coupon_code = document.getElementById("coupon_code").value;
					if(parseInt(data[0].error)==1)
					{
						alert(coupon_code + Joomla.JText._('COP_EXISTS'));
						document.getElementById("coup_button").removeAttribute("disabled");
						return;
					}

					if(parseFloat(data[0].value)>0)
					{
						if(data[0].val_type == 1)
						{
							val = (data[0].value/100)*document.getElementById("total_amt_inputbox").value;
						}
						else
						{
							val = data[0].value;
						}

						finalvar=0
						techjoomla.jQuery("input[class='totalpriceclass']").each(function(){
						finalvar=parseFloat(techjoomla.jQuery(this).val())+parseFloat(finalvar);
						});

						amt=parseFloat(finalvar)-parseFloat(val);

						if(parseFloat(amt)<= 0)
						{
							amt=0;
						}

						if(isNaN(finalvar))
						{

							amt=0;
						}

						techjoomla.jQuery("#net_amt_pay_inputbox").val(amt)
						techjoomla.jQuery("#net_amt_pay").html(amt);
						var allow_taxation=techjoomla.jQuery("#allow_taxation").val();

						if(allow_taxation==1)
						{
							calculatetax(amt);
						}

						techjoomla.jQuery("#dis_cop_amt").html(""+val);
						techjoomla.jQuery("#dis_amt").show();
						techjoomla.jQuery("#dis_cop").show();
					}
				}
			});
		}
	}
}

function caltotal(avalble,totalpriceid,count,obj,unlimited,peruserlimit,maxUserPerTicket)
{
	total_calc_amt2=0;

	techjoomla.jQuery("input[class='input-small type_ticketcounts']").each(function()
	{
		total_calc_amt2=parseInt(total_calc_amt2)+parseInt(techjoomla.jQuery(this).val())
	});

	/* If entered no of ticket is greater than no of tickets allowed*/
	if(parseInt(peruserlimit)< parseInt(total_calc_amt2))
	{
		alert(maxUserPerTicket);
		obj["value"]=0;
		return;
	}

	/* If entered no of ticket is greater than no of tickets allowed */

	if ((parseInt(unlimited)!=1))
	{

		if(parseInt(avalble)< parseInt(obj['value']))
		{
			alert(Joomla.JText._('ENTER_LESS_COUNT_ERROR'));
			obj["value"]=0;
			return;
		}
	}

	total_calc_amt=0;
	totalprice=count*parseFloat(obj['value']);

	if(isNaN(totalprice))
		totalprice=0;
	totalprice = totalprice.toFixed(2);
	techjoomla.jQuery("#ticket_total_price"+totalpriceid).html(totalprice);

	techjoomla.jQuery("#ticket_total_price_inputbox"+totalpriceid).val(totalprice);

	techjoomla.jQuery("input[class='totalpriceclass']").each(function()
	{
		total_calc_amt=parseFloat(total_calc_amt)+parseFloat(techjoomla.jQuery(this).val())
	});

	var couponenable=0;
	if(parseInt(total_calc_amt)==0)
	{
		techjoomla.jQuery("#cooupon_troption").hide();
	}
	else
	{
		couponenable=1;
		techjoomla.jQuery("#cooupon_troption").show();
	}

	if(techjoomla.jQuery("#coupon_chk").is(":checked") && techjoomla.jQuery("#coupon_code").val()!="")
	{
		applycoupon();
	}

	if(isNaN(total_calc_amt))
	{
		total_calc_amt=0;
	}

	total_calc_amt = total_calc_amt.toFixed(2);
	techjoomla.jQuery("#total_amt").html(total_calc_amt);
	techjoomla.jQuery("#total_amt_inputbox").val(total_calc_amt);
	techjoomla.jQuery("#net_amt_pay").html(total_calc_amt);
	techjoomla.jQuery("#net_amt_pay_inputbox").val(total_calc_amt);
	var allow_taxation=techjoomla.jQuery("#allow_taxation").val();

	if(allow_taxation==1)
	{
		calculatetax(total_calc_amt);
	}
}

function calculatetax(amt)
{
	techjoomla.jQuery.ajax(
	{
		url: jtBaseUrl+"index.php?option=com_jticketing&task=buy.applytax&tmpl=component&total_calc_amt="+amt,
		type: "GET",
		dataType: "json",
		success: function(taxdata)
		{
			if (taxdata!=null  && parseFloat(taxdata.taxvalue)>0)
			{
				techjoomla.jQuery("#order_tax").val(parseFloat(taxdata.taxvalue));
				var taxamt=techjoomla.jQuery("#order_tax").val();
				taxamt = parseFloat(taxamt)
				taxamt = taxamt.toFixed(2);
				techjoomla.jQuery("#tax_to_pay").html(taxamt);
				techjoomla.jQuery("#tax_to_pay_inputbox").val(taxamt);
				var net_amt_after_tax=parseFloat(taxamt)+parseFloat(amt)
				net_amt_after_tax = net_amt_after_tax.toFixed(2);
				techjoomla.jQuery("#net_amt_after_tax").html(net_amt_after_tax);
				techjoomla.jQuery("#net_amt_after_tax_inputbox").val(net_amt_after_tax);
				techjoomla.jQuery("#tax_tr").show();
			}
			else
			{
				techjoomla.jQuery("#order_tax").val(0);
				techjoomla.jQuery("#tax_to_pay").html(0);
				techjoomla.jQuery("#tax_to_pay_inputbox").val(0);
				var net_amt_after_tax=parseFloat(amt)
				net_amt_after_tax = net_amt_after_tax.toFixed(2);
				techjoomla.jQuery("#net_amt_after_tax").html(amt);
				techjoomla.jQuery("#net_amt_after_tax_inputbox").val(amt);
				techjoomla.jQuery("#tax_tr").hide();
			}
		}
	});
}

function checkforalpha(el)
{
	var i =0;
	for(i=0;i<el.value.length;i++)
	{
		if((el.value.charCodeAt(i) > 64 && el.value.charCodeAt(i) < 92) || (el.value.charCodeAt(i) > 96 && el.value.charCodeAt(i) < 123)) { alert(Joomla.JText._('COM_JTICKETING_ENTER_NUMERICS')); el.value = el.value.substring(0,i); break;}
	}

	if(el.value<0)
	{
		alert(Joomla.JText._('COM_JTICKETING_ENTER_AMOUNT_GR_ZERO'));
	}

	if(el.value % 1 !== 0)
	{
		alert(Joomla.JText._('COM_JTICKETING_ENTER_AMOUNT_INT'));
		el.value=0;
		return false;
	}
}

function jtShowFilter()
{
	techjoomla.jQuery("#jthorizontallayout").toggle();
}
