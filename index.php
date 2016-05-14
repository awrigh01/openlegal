<?php


?>

<html>
	<head>
		<title>Open Legal - the easiest way to create contracts</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
		<link rel="stylesheet" href="openlegal.css">
		<script>

			
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
				var regExp2 = /\*\*(.*?)\*\*/g;
				var description = contract.match(regExp2);
				var clean_description = description[0].replace(/\*\*/g,"");

				//find optional contract variables
				var regExp3 = /\{\{(.*?)\}\}/g;
				var optional_matches = contract.match(regExp3);

				//find non-optional contract variables
				var regExp4 = /\[\[(.*?)\]\]|\{\{(.*?)\}\}/g;
				var matches = contract.match(regExp4);
				var matches = jQuery.unique(matches);

				//generate form
				var form = "<h1>"+clean_title+"</h1><div id='contract_description'>"+clean_description+"</div>";
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
				
				//format contract text
				$("#contract_text").addClass("draft");
				reg = new RegExp('\\{'+title+'\\}', 'gi');
				var contract_formated = contract;
				contract_formated = contract_formated.replace(reg,"<b class='title'>"+clean_title+"</b>");
				alert(contract_formated);

				//set height of window
				h=$("#contract_text").height()+120;
				$("#contract_text_container").css({'height':h});

				//populate contract form
				$("#contract_form").show();
				$("#contract_form").html(form);
			});

			//clear/repopulate input values on forms
			$(document).on("focus", ".input_for_variable",function(){
				$(this).val("");
			});

			$(document).on("blur", ".input_for_variable",function(){
				var id = $(this).attr('id');
				id = id.replace(/\_/g," ")
				$(this).val(id);
			});

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