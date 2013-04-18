<%@ Page Language="C#" AutoEventWireup="true" CodeFile="Default.aspx.cs" Inherits="js_cal_Default" %>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
    <title>Untitled Page</title>

    <link rel="stylesheet" href="themes/forest.css" />
    <link rel="stylesheet" href="themes/layouts/small.css" />
    <script type="text/javascript" src="src/calendar.js"></script>
    <script type="text/javascript" src="lang/calendar-en.js"></script>

</head>
<body>
    <form id="form1" runat="server">
    <div>
        <asp:TextBox ID="txt" runat="server" />
            <script type="text/javascript">//<![CDATA[
      Zapatec.Calendar.setup({
        firstDay          : 1,
        weekNumbers       : false,
        showOthers        : true,
        electric          : false,
        inputField        : "txt",
        button            : "txt",
        ifFormat          : "%m/%d/%Y",
        daFormat          : "%Y/%m/%d"
      });
    //]]></script>
    </div>
    </form>
</body>
</html>
