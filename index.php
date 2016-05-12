<?php


?>

<html>
	<head>
		<title>Open Legal</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
		<script>
			$( document ).ready(function() {
    			$.ajax({
				  url: 'test.txt',
				  dataType: "text",
				  success: function(data){
				     //load content to div
				     $("#contract_text").html(data);
				     $("#contract_text").append('<div id="contract_edit">Edit</div>');
				  }
				});

			});
			$(document).on("click", "#contract_edit", function(){
				//remove edit button
				$("#contract_edit").remove();

				// save the html and height 
				var divHtml = $("#contract_text").html();
				var divHeight = $("#contract_text").height();
				var divWidth = $("#contract_text").width()+76;
				// create a dynamic textarea
				var editableText = $("<textarea id='contract_textarea'></textarea>");
				
				// fill the textarea with the div's text
				editableText.val(divHtml);
				
				// replace the div with the textarea and set height

				$("#contract_text").replaceWith(editableText);
				$("#contract_textarea").css({"height":divHeight,"width":divWidth});

			});
		</script>
		<style>
			body {
				margin:0;
				font-size: 16px;
			}
			#top {
				background: black;
				color:white;
				font-size:36px;
				padding:6px 9px;
			}
			#contract_text, #contract_textarea {
				position: relative;
				border-style: solid;
    			border-width: 1px 2px 2px 1px;
    			border-color:#181818;
				padding:36px;
				white-space: pre-line;
				width:80%;
				margin:auto;
				background:white;
				z-index: 1;
			}

			#contract_textarea {
				display:block;
				font-family: times;
				font-size:16px;

			}

			#contract_edit, #contract_save {
				width:50px;
				height:36px;
				position: absolute;
				top:11px;
				right:20px;
				z-index: 3;
				font-size:22px;
				background-color: #c3c3c3;
				color:white;
				text-align: center;
				line-height: 36px;
				cursor: pointer;
			}

			#contract_container {
				padding:30px 0;
				background:#f2f2f2;
			}
		</style>
	</head>
	<body>
		<div id="top">
			Open Legal
		</div>
		<div id="contract_container">
			<div id="contract_text"></div>
		</div>
	</body>
</html>