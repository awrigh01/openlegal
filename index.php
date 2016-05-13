<?php


?>

<html>
	<head>
		<title>Open Legal</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
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

				//find non-optional contract variables
				var regExp4 = /\[\[(.*?)\]\]/g;
				var matches = contract.match(regExp4);
				var matches = jQuery.unique(matches);

				//generate form
				var form = "<h1>"+clean_title+"</h1><div id='contract_description'>"+clean_description+"</div>";
				for (var i = 0; i < matches.length; i++) {
    				var res = matches[i].replace("[[","");
    				res = res.replace("]]","");
    				res_id = res.replace(/\ /g,"_")
    				form += "<div class='contract_variable'><input type='text' class='input_for_variable' id='"+res_id+"' value='"+res+"'/></div>";
				}
				form += "<input type='submit' id='form_submit' value='Create Contract'>";
				$("#contract_text").hide();
				$("#contract_form").html(form);
			});

			$(document).on("focus", ".input_for_variable",function(){
				$(this).val("");
			});


		</script>
		<style>
			body {
				margin:0;
				font-size: 16px;
				font-family:verdana;
			}
			#top {
				background: black;
				color:white;
				font-size:36px;
				padding:6px 9px;
				letter-spacing: -3px;
			}
			#contract_text_container {
				border-style: solid;
    			border-width: 1px 2px 2px 1px;
    			border-color:#181818;
				padding:36px;
				white-space: pre-line;
				width:80%;
				margin:auto;
				background:white;
				z-index: 1;
				font-family:Times !important;
			}

			#contract_textarea {
				display:block;
				font-family: times;
				font-size:16px;
				border: none;

			}

			#contract_container {
				position: relative;
				padding:30px 0;
				background:#f2f2f2;
			}

			#contract_actions {
				position: absolute;
				right:20px;
				top:11px;
				z-index: 3;
				width:90px;
				font-size:18px;
				color:white;
				text-align: center;
				cursor: pointer;
				font-family:verdana !important;
			}

			#contract_actions div {
				margin: -8px 0 0;
				height: 33px;
				padding:6px;
				cursor: pointer;
				line-height: 33px;
				z-index: 4;
				border-radius: 6px;
			}

			#contract_draft {
				background-color: orange;
			}

			#contract_preview {
				background-color: blue;
			}

			.edit {
				background-color: #1ed760;	
			}

			.save {
				background-color: #ce5054;
			}

			.contract_variable {
				margin:0 0 11px 0;
			}

			#contract_form {
				font-family: verdana;
				font-size:18px;
				margin:-30px 0 0 0;
			}

			#contract_form h1 {
				margin:0 0 18px 0;
			}

			#contract_description {
				color:#ccc;
				font-style: italic;
				margin:0 0 18px 0;
			}

			.contract_variable input {
				width:300px;
				border:1px #a6a6a6 solid;
				border-radius: 3px;
				font-size: 18px;
				padding:0 6px;
				line-height:30px;
				color:#ccc;
			}

			#form_submit {
				margin:11px 0 0 0;
				height:36px;
				width:240px;
				color:#fff;
				background-color: red;
				border: none;
				font-size: 18px;
				line-height: 36px;
				border-radius: 6px;
			}

			#top-small {
				font-size: 16px;
				letter-spacing: -1px;
			}
		</style>
	</head>
	<body>
		<div id="top">
			<b>openlegal</b> <span id="top-small">| the easiest way to create contracts</span>
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