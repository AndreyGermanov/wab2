<?php include "boot.php" ?>
<html>
	<head>
		<title>Общероссийский благотворительный общественный фонд "Содружество"</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">	
		<?php include "scripts.php" ?>
		<style>	
			.frame {
				border-width:2px;
				border-style:solid;
				border-color:#AAAAAA;	
			}
			
			.form {
				border-width:1px;
				border-style:solid;
				border-color:#AAAAAA;
				background-color:#d5e5ff;	
			}
			
			.menuline {
				background-color:#0055d4;
				color:#FFFFFF;
			}
			
			.menuitem {
				color:#FFFF88;	
				font-weight:bold;
				font-variant:small-caps;
				font-size:24px;
				font-family: Arial;
			}

			.menuitem:hover {
				background-color:#0033a4;
				color:#ffffff;
				cursor:pointer;
			}
			
			.header_text {
				color:#FFFFFF;	
				font-weight:bold;
				font-variant:small-caps;
				font-size:24px;
				font-family: Arial;
			}

			.header_text_black {
				color:#000000;	
				font-weight:bold;
				font-size:18px;
				font-family: Arial;
			}
			
			.header_text_small {
				color:#FFFFFF;	
				font-weight:bold;
				font-size:20px;
				font-family: Arial;
			}

			.header_link {
				color:#FFFFFF;	
				font-weight:bold;
				font-variant:small-caps;
				font-size:16px;
				font-family: Arial;
			}
			
			.input {
				border-style:1px;
				border-color:#666666;
				border-style:solid;
			}
			
			.input:hover {
				border-style:1px;
				border-color:#000000;
				border-style:solid;
				background-color:#FFFF88;
			}			
			
			.input:focus {
				border-style:1px;
				border-color:#000000;
				border-style:solid;
				background-color:#FFFF88;
			}			
			
			span.title {
				font-weight:bold;
				font-size:50px;
				font-family:Arial,Sans;
				font-variant:small-caps;
				margin-left:5px;
				margin-top:10px;
				margin-right:5px;
			}
			
			p {
				margin-left:5px;
				font-family:Arial,Sans;
				font-size:13px;
				margin-right:15px;	
				text-align:justify;			
			}
			
			.text {
				font-family:Arial,Sans;
				font-size:13px;
				font-weight:bold;			
			}

			.purple {
				color:#aa0088;
				font-weight:bold;
			}
			
			a {
				color:#aa0088;		
				text-decoration:none	
			}
			
			a:hover {
				text-decoration:underline
			}
			
			hr {
				color:#003388;
				background-color:#003388;
				height:3px;			
			}
					
        	.submenu {
        		border-style:solid;
        		border-color:#000000;
        		border-width:0px;
				background-color:#0033a4;
        		position:absolute;
        		z-index:2;
        	}        	        						
		</style>
	</head>
	<body>
		<table bgcolor="#FFFFFF" width="1000" cellpadding="2" cellspacing="0" align="center" class="frame" style="height:100%;opacity:0.8;z-index:0">
			<tbody>
			<tr valign="top">
				<td  colspan="2">
					<div align="center">
						<img src='content/images/header.png'/>
					</div>
				</td>
			</tr>			
			<tr valign="middle" class="menuline" width="100%" cellpadding="0" cellspacing="0">
				<td  colspan="2">
					<table cellpadding="2">
						<tbody>
						<tr valgin="middle">
							<td>
								<div align="left" id="TableMenu_1" object="TableMenu_1">
									<table celpadding="2" cellspacing="0" id="TableMenu_1_mainmenu">
										<tbody>
										<tr valign="top">
											<td nowrap="true" class="menuitem">
												о фонде
											</td>
											<td>
												&nbsp;
											</td>
											<td nowrap="true" class="menuitem">
												посетителю
											</td>
										</tr>
										<tr valign="middle">
											<td>
												<table type="menu" class="submenu" style="display:none" cellpadding="3" cellspacing="0" id="TableMenu_1_about">
													<tbody>
														<tr valign="top">
															<td class="menuitem" nowrap="true">информация</td>
														</tr>													
														<tr valign="top">
															<td class="menuitem" nowrap="true">новости и события</td>
														</tr>													
														<tr valign="top">
															<td class="menuitem" nowrap="true">контакты</td>
														</tr>													
														<tr valign="top">
															<td class="menuitem" nowrap="true">друзья</td>
														</tr>													
														<tr valign="top">
															<td class="menuitem" nowrap="true">как помочь</td>
														</tr>													
													</tbody>												
												</table>
											</td>
											<td style="display:none">
												&nbsp;
											</td>
											<td>
												<table type="menu" class="submenu" style="display:none" cellpadding="3" cellspacing="0" id="TableMenu_1_locked">
													<tbody>
														<tr valign="top">
															<td class="menuitem" nowrap="true">документы</td>
														</tr>													
														<tr valign="top">
															<td class="menuitem" nowrap="true">спец. литература</td>
														</tr>													
														<tr valign="top">
															<td class="menuitem" nowrap="true">документы фонда</td>
														</tr>													
														<tr valign="top">
															<td class="menuitem" nowrap="true">форум</td>
														</tr>													
														<tr valign="top">
															<td class="menuitem" nowrap="true">доска объявлений</td>
														</tr>													
													</tbody>												
												</table>
											</td>
										</tr>
										</tbody>
									</table>
								</div>
							</td>
							<td width="100%">
								<div align="right">
									<table celpadding="2" cellspacing="2">
										<tbody>
										<tr valign="middle">
											<td class="header_text">
												логин:
											</td>
											<td class="menuitem">
												<input type="text" class="input" style="width:100px"/>
											</td>
											<td class="header_text">
												пароль:
											</td>
											<td class="menuitem">
												<input type="text" class="input" style="width:100px"/>
											</td>
											<td class="header_text">
												<input type="submit" value="Вход" class="input"/>
											</td>
											<td class="menuitem">
												<a href="#" class="header_link">
													регистрация
												</a>
											</td>
										</tr>
										</tbody>
									</table>
								</div>
							</td>							
						</tr>
						</tbody>
					</table>
				</td>
			</tr>
			
			<tr style="height:100% "valign="top">
			    <td style="height:100%" width="70%">
			    	<span class="title">
			    		о фонде
			    	</span>
			    	<p>
			    		<img src="content/images/Vydelenie_009.png" align="left" hspace="10"/>
						Белгородский филиал Общероссийского благотворительного общественного фонда «Cодружество» создан в 2013 году. Также еще 48 регионах Российской Федерации  созданы отделения Фонда, которые поддерживают его цели и задачи.
					</p>

					<p>
						Общероссийский благотворительный общественный фонд «Cодружество» создан в 2007 году благодаря объединению усилий врачей (эпилептологов, неврологов, психиатров) и пациентов (их родственников и близких).
					</p>
					<p>
					С 2009 года фонд является действительным членом Международного Бюро по Эпилепсии (International Bureau for Epilepsy — IBE).
					</p>
					<p class="purple">
					Целями Фонда «Содружество» являются:
					</p>
<p>
1. Создание условий для раскрытия потенциальных способностей больных эпилепсией, создания равных возможностей для них и полноценной их интеграции в гражданское общество.
</p>
<p>
2. Борьба с неоправданными социальными ограничениями, накладываемыми на больных Распространение достоверной информации об эпилепсии и современных возможностях её излечения среди организаторов здравоохранения, врачей, больных и всего населения. Издание журнала и тематических брошюр. Участие в международном движении «Эпилепсия из тени» (“Epilepsy out of the shadow”).
</p>
<p>
3. Помощь больным в получении квалифицированной медицинской помощи. Организация обследования и лечения инвалидов на дому. Посильное оказание помощи больным и их близким в психологических, юридических и педагогических вопросах.
</p>
<p>
4. Содействие использованию современных методов лечения и диагностики для каждого больного, издание научной литературы, проведение конференций с привлечением выдающихся специалистов. Распространение специальных образовательных программ («школ») для детей и взрослых, страдающих эпилепсией. Организация их досуга и семейного отдыха.
</p>
 
<p>
С уважением руководитель Белгородского регионального отделения фонда «Содружество»: Киктенко А. А.
</p>			    	
			    </td>
			    <td width="30%"><br/>
			    	<div style="margin-right:10px">
			    	<table width="100%" cellpadding="2" cellspacing="0" class="form" style="opacity:1">
			    		<tbody>
			    		<tr valign="top">
			    			<td class="menuline">
			    				<span class="header_text_small">ЗАДАТЬ ВОПРОС:</span>
			    			</td>
			    		</tr>
			    		<tr valign="top">
			    			<td>
								<form action="index.php" method="post" target="{object_id}_innerFrame">
									<table width="100%" cellpadding="3" cellspacing="0">
										<tbody>
										<tr valing="top">
											<td class="header_text_black">Имя:</td>
											<td width="100%">
												<input class="input" type="text" id="humanName" name="humanName" style="width:100%">
											</td>
										</tr>
										<tr valing="top">
											<td class="header_text_black">E-mail:</td>
											<td width="100%">
												<input class="input" type="text" id="humanEmail" name="humanEmail" style="width:100%">
											</td>
										</tr>
										<tr valign="top">
											<td colspan="2" class="header_text_black">
												Сообщение:
											</td>
										</tr>		
										<tr valign="top">
											<td colspan="2">
												<textarea class="input" id="humanText" name="humanText" style="width:100%"></textarea>
											</td>
										</tr>
										<tr valign="top">
											<td colspan="2" nowrap="true" class="header_text_black">
												Контрольное слово:
											</td>
										</tr>		
										<tr valign="top">
											<td colspan="2">
												<img id="kaptcha_img" src="tools/kcaptcha/index.php?{session_name}={session_id}"><br></br>
												<input class="input" id="kaptcha" type="text" name="keystring" style="width:100%">
												
											</td>
										</tr>
										<tr valign="top">
											<td colspan="2" nowrap="true"">
												<div align="right">
													<input type="submit" class="input" id="OK" name="OK" value=" Отправить "/>			
												<input type="hidden" name="action" object="{object_id}" id="action" value="submit">
												<input type="hidden" name="object_id" object="{object_id}" id="object_id" value="{object_id}">
												<input type="hidden" name="hook" object="{object_id}" id="hook" value="3">				
												<input type="hidden" name="ajax" object="{object_id}" id="ajax" value="true">				
								                <iframe src="" name="{object_id}_innerFrame" id="innerFrame" style="display:none" width="100%"></iframe>
												</div>
											</td>
										</tr>	
										</tbody>	
									</table>	
								</form>
							</td>							
						</tr>
						</tbody>
					</table>					   
					</div> 
			    </td>
			</tr>
			<tr valign="top">
				<td colspan="2">
					<hr noshade="true">
					<table width="100%" cellpadding="0" cellspacing="0">
					<tbody>
						<tr valign="top">
							<td class="text"><div align="left">&copy; 2012 Общероссийский благотворительный общественный фонд "Содружество"</div></td>
							<td class="text"><div align="right">Разработка сайта: <a href="http://www.lvacompany.ru">ООО "ЛВА"</div></td>
						</tr>
					</tbody>					
					</table>
				</td>
			</tr>
			</tbody>
		</table>
		<div style="position:fixed;left:0px;top:0px;width:100%;height:100%;z-index:-2;overflow:auto"><img src="/content/images/clouds.jpg" style="width:100%;height:100%;z-index:-2"/></div>
		<script>
			var menu = new TableMenu("TableMenu_1");objects.add(menu,"");menu.init($("TableMenu_1_mainmenu"));
			if (window["attachEvent"]!=null)
				window.attachEvent("onclick",menu.hideAllMenus);
			else
				window.addEventListener("click",menu.hideAllMenus);			
		</script>
	</body>
</html>