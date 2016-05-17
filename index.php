<?php


?>

<html>
	<head>
		<title>Open Legal - the easiest way to create contracts</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
		<link rel="stylesheet" href="openlegal.css">
		<script>
		
			function escapeRegExp(str) {
  				return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
			}

			function format_contract() {
				//format contract_text 
				
				var formatted_contract_text = $("#contract_text").html();
				formatted_contract_text = escapeRegExp(formatted_contract_text);

				//get rid of title and description
				formatted_contract_text = formatted_contract_text.replace(/(\=(.*?)\=)|\@\@(.*?)\@\@/g,"");
				formatted_contract_text = formatted_contract_text.replace(/\n\n\n\n/g,"");

				//kludge to fix the parens issue -- need to fix
				//formatted_contract_text = formatted_contract_text.replace(/\(/g,"@");
				//formatted_contract_text = formatted_contract_text.replace(/\)/g,"!");
				//formatted_contract_text = formatted_contract_text.replace(/\[/g,"&");
				//formatted_contract_text = formatted_contract_text.replace(/\]/g,"=");

				//change formatting of sections
				var sections = formatted_contract_text.match(/\'\'([a-zA-Z'[]\s]+)\'\'|\'\'\'\'([a-zA-Z\s]+)\'\'\'\'/g);

				for (var i = 0; i < sections.length; i++) {
					
					res=sections[i];
					comma_count = sections[i].split("'").length - 1;
				
					if (comma_count==4) {
						var res_replace = res.replace(/\'\'/g,"");
						res_replace = "<b>"+res_replace+"</b>";
					} else {
						var res_replace = res.replace(/\'\'\'\'/g,"");
						res_replace = "<b><u>"+res_replace+"</u></b>";
					}

					var re = new RegExp(res,"gim");
					formatted_contract_text = formatted_contract_text.replace(re, res_replace);
				}

				//hide the optional text
				var opt_lang = formatted_contract_text.match(/\{\{(.*?)\}\}/g);
				for (var i=0; i < opt_lang.length; i++) {
					
					res=opt_lang[i];
					var res_opt_lang = res.replace(/\{|\}/g,"");
					res_opt_lang = "<div class='opt_lang'>"+res_opt_lang+"</div>";
					var re = new RegExp(res,"gim");
					console.log(re.test(formatted_contract_text));
					formatted_contract_text = formatted_contract_text.replace(re, res_opt_lang);
					
				}

				//number main sections
				var num_sections = formatted_contract_text.match(/\#(.*)\#/g);
				
				for (var i=0; i < num_sections.length; i++) {
					
					res=num_sections[i];
					var par_num = i+1;
					var res_num_replace = res.replace(/\#/g,"");
					res_num_replace = "<div class='section'>"+par_num+"."+res_num_replace+"</div>";
					var re = new RegExp(res,"g");
					formatted_contract_text = formatted_contract_text.replace(re, res_num_replace);
				}

				//number the subsections

				//fix the kludge
				//formatted_contract_text = formatted_contract_text.replace(/\@/g,"(");
				//formatted_contract_text = formatted_contract_text.replace(/\!/g,")");
				//formatted_contract_text = formatted_contract_text.replace(/\[/g,"&");
				//formatted_contract_text = formatted_contract_text.replace(/\]/g,"=");
				//formatted_contract_text=formatted_contract_text.replace(/\\/g,"");
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
				var regExp1 = /\=(.*?)\=/g;
				var title = contract.match(regExp1);
				var clean_title = title[0].replace(/\=/g,"");

				//find contract description
				var regExp2 = /\@\@(.*?)\@\@/g;
				var description = contract.match(regExp2);
				var clean_description = description[0].replace(/\@\@/g,"");


				//find optional contract variables
				var regExp3 = /\{\{(.*?)\}\}/g;
				var optional_matches = contract.match(regExp3);

				//find non-optional contract variables
				var regExp4 = /\[\[(.*?)\]\]|\{\{(.*?)\}\}/g;
				var matches = contract.match(regExp4);
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

	    				//split optional variable into question and string with variable
    					var ov = r.split("|");

    					//grab optional variable from string
    					var regExp5 = /\[\[(.*?)\]\]/g;
						var v = ov[1].match(regExp5);
						var v = v[0].replace(/\[\[|\]\]/g,"");
    					v_id = v.replace(/\ /g,"_")

    					//create form input
    					form += "<div class='contract_variable'>"+ov[0]+"<div id='radio_"+i+"'><input type='radio' id='radio_"+i+"_yes' class='radio_yes radio_for_optional_variable' name='radio_"+i+"'>Yes<input type='radio' id='radio_"+i+"_no' class='radio_no radio_for_optional_variable' name='radio_"+i+"' checked>No</div><div class='contract_variable_optional' id='contract_variable_optional_"+i+"'><input type='text' class='input_for_variable' id='"+v_id+"' value='"+v+"'/></div></div>";

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
			});

			//clear/repopulate input values on forms
			$(document).on("focus", ".input_for_variable",function(){
				if ($(this).hasClass("filled")==false) {
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
					$(this).addClass("filled");
					$(this).addClass("on");
				}

			});

			$(document).on("keyup", ".input_for_variable",function(){

				//get name of variable
				var id = $(this).attr('id');
				variable = id.replace(/\_/g," ");

				//get value of field
				var val = $(this).val();
				
				if ($(this).hasClass("filled")==false) {

						//set variable
						var variable = "[["+variable+"]]";

						//grab contract text
						var updated_contract_text = $("#contract_text").html();
						
						//update text
						updated_contract_text = updated_contract_text.replace(variable,"<b id='filled_"+id+"'>"+val+"</b>");

						$(this).addClass("filled");

					} else {

						id="filled_"+id;
						$("#"+id).html(val);
					}
					
					$("#contract_text").html(updated_contract_text);

			})


			//on click of yes display variable in form
			$(document).on("click", ".radio_yes",function(){
				id = $(this).attr("id");
				id_n = id.match(/\d+/g);
				d = 'contract_variable_optional_'+id_n[0];
				$('#'+d).show();
			});

			$(document).on("click", ".radio_no",function(){
				id = $(this).attr("id");
				id_n = id.match(/\d+/g);
				d = 'contract_variable_optional_'+id_n[0];
				$('#'+d).hide();
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