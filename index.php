<?php


?>

<html>
	<head>
		<title>Open Legal - the easiest way to create contracts</title>
		<script src="jquery.min.js"></script>
		<script src="verbal.js"></script>
		<link rel="stylesheet" href="openlegal.css">
		<script>
		
			//a function to escape text from the contract so that it can form a valid regular expression
			function escapeRegExp(str) {
  				return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
			}


			//this function formats the contract 
			function format_contract() {
				
				var formatted_contract_text = $("#contract_text").html();
				$("#contract_text").css({"white-space":"normal"});
				
				//get rid of title and description
				formatted_contract_text = formatted_contract_text.replace(/(\=(.*?)\=)|\@\@(.*?)\@\@/g,"");
				formatted_contract_text = formatted_contract_text.replace(/\n\n\n\n/g,"");

				//change formatting
				var format = formatted_contract_text.match(/\'\'([^']+)\'\'|\'\'\'\'([^']+)\'\'\'\'/g);

				for (var i = 0; i < format.length; i++) {
					
					res=format[i];
					comma_count = format[i].split("'").length - 1;
				
					if (comma_count==4) {
						var res_replace = res.replace(/\'\'/g,"");
						res_replace = "<b>"+res_replace+"</b>";
					} else {
						var res_replace = res.replace(/\'\'\'\'/g,"");
						res_replace = "<b><u>"+res_replace+"</u></b>";
					}
					escaped_res = escapeRegExp(res);
					var re = new RegExp(escaped_res,"g");
					formatted_contract_text = formatted_contract_text.replace(re, res_replace);
				}
				
				
				//hide the optional text
				var opt_lang = formatted_contract_text.match(/\{\{(.*?|[\n\r\t])\}\}/g);
				for (var i=0; i < opt_lang.length; i++) {
					
					res=opt_lang[i];
					//console.log(res);
					var escaped_res = escapeRegExp(opt_lang[i]);
					var res = "<span class='optional-clause'>"+res.replace(/\{|\}/g,"")+"</span>";
					var re = new RegExp(escaped_res,"g");
					formatted_contract_text = formatted_contract_text.replace(re, res);
					
				}

				//number main sections
				var sections = formatted_contract_text.match(/\#(.*|[\n\r\t])\#/g);

				for (var i=0; i < sections.length; i++) {
					
					var num = i+1;
					var res=sections[i];
					console.log(res);
					
					var sec="";
					if (i==0) {
						sec+="<ol id='main-sections'>";
					}
					sec+="<li class='contract-section'>"+res.replace(/(\r\n|\n|\r|\t|\#)/g,"")+"</li>";
					if(i==(sections.length-1)) {
						sec="</ol>";
					}
					escaped_res = escapeRegExp(sections[i]);
					var re = new RegExp(escaped_res,"g");
					formatted_contract_text = formatted_contract_text.replace(re, sec);
				}

				//number the subsections
				var sub_sections = formatted_contract_text.match(/\*(.*?|[\n\r\t])\*/g);

				for (var i=0; i < sub_sections.length; i++) {
					
					var res=sub_sections[i];
					var sub_sec="";
					if(i==0) {
						sub_sec+="<ol class='sub-sections'>";
					}
					sub_sec+="<li class='"+((i==0)?" first":"")+"'>"+res.replace(/(\*)/g,"")+"</li>";
					//var num = String.fromCharCode(97 + i); // where n is 0, 1, 2 ...
					//res="<div class='contract-sub-section"+((i==0)?" first":"")+"'>"+"("+num+")"+res.replace(/(\*)/g,"")+"</div>";
					if(i==(sub_sections.length-1)) {
						sub_sec+="</ol>";
					}

					escaped_res = escapeRegExp(sub_sections[i]);
					var re = new RegExp(escaped_res,"g");
					formatted_contract_text = formatted_contract_text.replace(re, sub_sec);
				}

				//replace newlines with html line breaks
				var expression = VerEx().find('\n');
				formatted_contract_text = expression.replace(formatted_contract_text, '<br>');

				return formatted_contract_text;
			}

			$( document ).ready(function() {
    			
    			//load txt contract

    			$.ajax({
				  url: 'test.txt',
				  dataType: "text",
				  success: function(data){
				     //load content to div
				     $("#contract_text").html(data);
				  }
				});

			});
			
			//created editable contract
			$(document).on("click", ".edit", function(){

				//contract 
				$("#contract_text").show();
				$("#contract_form").hide();

				// save the html and height 
				var divHtml = $("#contract_text").html();
				var divHeight = $("#contract_text").height();
				var divWidth = $("#contract_text").width();
				// create a dynamic textarea
				var editableText = $("<textarea id='contract_textarea'></textarea>");
				
				// fill the textarea with the div's text
				editableText.val(divHtml);
				
				// replace the div with the textarea and set height

				$("#contract_text").replaceWith(editableText);
				$("#contract_textarea").css({"height":divHeight,"width":divWidth});
				$("#contract_action").removeClass("edit");
				$("#contract_action").addClass("save");
				$("#contract_action").html("Save");

			});

			//save edited contract
			$(document).on("click", ".save", function(){
				
				var contract = $("#contract_textarea").val();
				$.ajax({
				    url: 'save_txt.php',
				    type: 'POST',
				    data: { data: contract },
				    success: function(result) {
				        console.log("success");
				    }
				});
				$("#contract_textarea").replaceWith("<div id='contract_text'></div>");
				$("#contract_text").html(contract);
				$("#contract_action").removeClass("save");
				$("#contract_action").addClass("edit");
				$("#contract_action").html("Edit");

			});


			//create an editable form from a contract
			$(document).on("click","#contract_draft", function(){
				
				//grab contract text
				var contract = $("#contract_text").html();
				
				//find contract title
				var title = contract.match(/\=(.*?)\=/g);
				var clean_title = title[0].replace(/\=/g,"");

				//find contract description
				var description = contract.match(/\@\@(.*?)\@\@/g);
				var clean_description = description[0].replace(/\@\@/g,"");


				//find optional contract variables
				var optional_matches = contract.match(/\{\{(.*?)\}\}/g);

				//find non-optional contract variables;
				var matches = contract.match(/\[\[(.*?)\]\]|\{\{(.*?)\}\}/g);
				var matches = jQuery.unique(matches);

				//generate title and description
				var top = "<h1>"+clean_title+"</h1><div id='contract_description'>"+clean_description+"</div>";

				//generate form
				var form = "";
				for (var i = 0; i < matches.length; i++) {
    				
    				var res = matches[i];
    				
    				if(res.indexOf("{{")>=0){
    					
    					//get rid of enclosing squiglies
    					var r = res.replace(/\{\{|\}\}/g,"");

	    				//split optional variable into question and accompanying text
    					var ov = r.split("|");
    					
    					form+="<div class='contract_variable'>"+ov[1]

    					//check for variables in accompanying text
						var v = ov[2].match(/\[\[(.*?)\]\]/g);
						if(v) {
							v = v[0].replace(/\[\[|\]\]/g,"");
    						v_id = v.replace(/\ /g,"_")

    						//create form input
    						form += "<div id='radio_"+v_id+"'><input type='radio' id='radio_"+i+"_yes' class='radio_yes radio_for_optional_variable' name='radio_"+i+"'>Yes<input type='radio' id='radio_"+i+"_no' class='radio_no radio_for_optional_variable' name='radio_"+i+"' checked>No</div><div class='contract_variable_optional' id='contract_variable_optional_"+i+"'><input type='text' class='input_for_variable optional' id='"+v_id+"' value='"+v+"'/></div>";	
						} else {

							form += "<div id='radio_"+i+"'><input type='radio' id='radio_"+i+"_yes' class='radio_yes radio_for_optional_variable' name='radio_"+i+"'>Yes<input type='radio' id='radio_"+i+"_no' class='radio_no radio_for_optional_variable' name='radio_"+i+"' checked>No</div>";

						}

						form+="</div>";
						

    				} else {

 	   					res = res.replace(/\[\[|\]\]/g,"");
    					res_id = res.replace(/\ /g,"_");
    					form += "<div class='contract_variable'><input type='text' class='input_for_variable' id='"+res_id+"' value='"+res+"'/></div>";

    				}

    				
				}

				//add submit button
				form += "<input type='submit' id='form_submit' value='Create Draft Contract'>";
				
				//populate contract form
				$("#contract_text").html(format_contract());
				$("#contract_form").show();
				$("#contract_form").html(form);
				$("#contract_text_container").prepend(top);
				
				//fix br issue
				var final_text = $("#main-sections").html();
				final_text = final_text.replace(/\<br\>/g,"");
				$("#main-sections").html(final_text);
			});

			//clear/repopulate input values on forms
			$(document).on("focus", ".input_for_variable",function(){
				if ($(this).hasClass("filled-input")==false) {
					$(this).val("");	
				}
				$(this).addClass("on");
			});

			$(document).on("blur", ".input_for_variable",function(){

				//get name of variable
				var id = $(this).attr('id');
				variable = id.replace(/\_/g," ");

				//get value of field
				var val = $(this).val();
				
				//if no input then replace with default text
				if (val=="") {
					$(this).val(variable);	
					$(this).removeClass("on");
				} else {
					$(this).addClass("filled-input");
					$(this).addClass("on");
				}

			});

			//show optional text
			$(document).on("focus", ".optional",function(){
				id = $(this).attr("id");
				variable = id.replace(/\_/g," ");
				selected_option_text = $("span:contains('[["+variable+"]]')").html();
				if ($("span:contains('[["+variable+"]]')")) {
					new_text = selected_option_text.split("|");
					$("span:contains('[["+variable+"]]')").html(new_text[2]).show();
					$("span:contains('[["+variable+"]]')").attr("id","optional_"+id);	
				}			
			});

			$(document).on("click", ".radio_no",function(){
				p_id = $(this).parent().attr('id');
				s_id = "optional_"+p_id.replace("radio_","");
				if($("#"+s_id).length){
					$("#"+s_id).hide();
				}
				
			});

			$(document).on("keyup", ".input_for_variable",function(){

				//get name of variable
				var id = $(this).attr('id');
				variable = id.replace(/\_/g," ");

				//get value of field
				var val = $(this).val();
				
				if ($(this).hasClass("filled-input")==false) {

						//set variable
						var variable = "[["+variable+"]]";

						//grab contract text
						var updated_contract_text = $("#contract_text").html();
						
						//update text
						var re = new RegExp(escapeRegExp(variable),"g")
						updated_contract_text = updated_contract_text.replace(re,"<span class='filled f_"+id+"'>"+val+"</span>");

						$(this).addClass("filled-input");

					} else {

						id="f_"+id;
						$("."+id).html(val);
					}
					
					$("#contract_text").html(updated_contract_text);

			})


			//on click of yes display variable in form
			$(document).on("click", ".radio_yes",function(){
				
				//identifiy the variable from the ID of the form
				id = $(this).attr("id");
				id_n = id.match(/\d+/g);
				d = 'contract_variable_optional_'+id_n[0];
				
				//if variable show
				if ($('#'+d).length) {
					$('#'+d).show();
				//else just show the clause in the optional clause
				} else {
					var o_question = $(this).parent().parent().html().replace(/<div.*>/g,"");
					$(".optional-clause").each(function(){
						o = $(this).html();
						o_clause = o.split("|");
						if (o_question== o_clause[1]) {
							$(this).html(o_clause[2]);
							$(this).show().addClass('filled');
						}
					});
				}

				//check if optional field already filled in and show
				p_id = $(this).parent().attr('id');
				s_id = "optional_"+p_id.replace("radio_","");
				if($("#"+s_id).length) {
					$("#"+s_id).show();
				}
				


			});

			$(document).on("click", ".radio_no",function(){
				id = $(this).attr("id");
				id_n = id.match(/\d+/g);
				d = 'contract_variable_optional_'+id_n[0];
				if ($('#'+d).length) {
					$('#'+d).hide();
				} else {
					console.log("no variable");
				}
			});


		</script>
	</head>
	<body>
		<div id="top">
			<a href="index.php"><b>openlegal</b></a> <span id="top-small">| the free respository of law</span>
		</div>
		<div id="contract_container">
			<div id="contract_text_container">
				<div id="contract_form"></div>
				<div id="contract_text"></div>
				<div id="contract_actions">
					<div id="contract_draft">Draft</div>
					<div id="contract_action" class="edit">Edit</div>
					<div id="contract_preview">New</div>	
				</div>
			</div>
		</div>
	</body>
</html>