<html style='background: #a8a8a8;'>
<head>
<meta name='viewport' content='width=device-width, inital-scale=1,user-scalable=1'>
<link rel="stylesheet" type="text/css" href="stylesheet.css">
</head> <script>
function link(loc){
	document.documentElement.style.animation='fade-out 1s';
	setTimeout(function(){
		window.location.href = loc;
	}, 500);
}
</script>
<body>
<h1 style='margin-left:1%;margin-top:1%;'>
<div class='down'>
<a href="javascript:link('../index.php')" style='color: #000000;'>
Copeland
</a>
</div>
<div class='right'>
<a href="javascript:link('../index.php')" style='color: #404040;'>
Web
</a>
</div>
<div class='up'>
<a href="javascript:link('../index.php')" style='color: #646464;'>
Design
</a>
</div>
</h1>
<table style='margin-left:5%'>
<tr>
<td>
<label>Age:</label></br>                           <input onkeyup='ProgramLoop()' onclick='ProgramLoop()' id='ageentry' type='number' value=25></input></br></br>
<label>Current Savings:</label></br>               <input onkeyup='ProgramLoop()' onclick='ProgramLoop()'  id='currententry' type='number' value=0></input></br></br>
<label>Goal Age:</label></br>                      <input onkeyup='ProgramLoop()' onclick='ProgramLoop()'  id='goalentry' type='number' value=65></input></br></br>
<label>Annualized Percent</br>Rate of Return:</label></br>        <input onkeyup='ProgramLoop()' onclick='ProgramLoop()'  id='rateentry' type='number' value=7></input></br></br>
<label>Additional Investment per Year:</label></br><input onkeyup='ProgramLoop()' onclick='ProgramLoop()'  id='additionalentry' type='number' value=5010></input></br>
<br>
<br>
<label style='font-size:22pt;font-style: bold;'>Final Amount:</label></br>
<label style='font-size:22pt;font-style: bold;' id='result'></label>
</td>
<td>
Touch or Drag Your Mouse Across the Graph to See Details!
<canvas id='project' width=410 height=480 style='border:1px solid black;margin-left:5%;margin-top:5%;'>
Your Browser does not support html 5!
</canvas>
</td>
</tr>
</table>
<span id="textruler" style="visibility:hidden; white-space: nowrap;">
</span>

<script>
//Track all the program state
var display=document.getElementById("project");
var ctx=display.getContext("2d");
var selection=0;
var freshInput=true;
var numberSelected=0;

//variables to control size of graph
var GRAPHBOTTOM=450;
var GRAPHLEFT=90;
var GRAPHRIGHT=390;
var GRAPHTOP=32;
var GRAPHWIDTH=GRAPHRIGHT-GRAPHLEFT;
var GRAPHHEIGHT=GRAPHBOTTOM-GRAPHTOP;

//helper function to calculate the length in pixels of a piece of text at a certain size
function VisualLength(str,font,pointsize)
{
    var ruler = document.getElementById("textruler");
    ruler.innerHTML = str;
    ruler.style.fontFamily=font;
    ruler.style.fontSize=pointsize;
    var offset=ruler.offsetWidth;
    ruler.innerHTML="";
    return offset;
}

//A simple class to make displaying variables easier
class displayedVariable{
	constructor(element){
		this.selectionNumber=numberSelected;
		this.element=element;
		numberSelected++;
	}
	GetValue(){
		return Number(document.getElementById(this.element).value);
	}
}
//All the input variables
age=new displayedVariable("ageentry");
currentRetirement=new displayedVariable("currententry");
currentRetirement.prefix="$";
goalAge=new displayedVariable("goalentry");
averageMarketReturnPercentage=new displayedVariable("rateentry");
averageMarketReturnPercentage.suffix="%";
additionalPerYear=new displayedVariable("additionalentry");
additionalPerYear.prefix="$";

//variables used for the graph math
var x=0;//X is the number of years
var amount= new Array(100);//amount (y-axis) is the dollar amount for a given year)
var cursor=-1;
var mouseX=0,mouseY=0;

//Listen for mouse moves so can update the graph cursor
document.getElementById('project').addEventListener('mousemove',function(event){
	mouseX=event.offsetX;
	mouseY=event.offsetY;

	//Calculate x relative to the graph coordinates
	x=event.offsetX;
	x-=GRAPHLEFT;
	x=x/(GRAPHWIDTH/(goalAge.GetValue()-age.GetValue()));
	x=Math.round(x);//Make x an integer so that it may be used as an index in amount

	//part a hard limit so the cursor cannot go past the left side or right side
	if(x>=goalAge.GetValue()-age.GetValue())
		x=goalAge.GetValue()-age.GetValue();
	else if(x<0)
		x=0;
	cursor=amount[x];

	//Call the loop so changes are rendered
	ProgramLoop();
},false);

//Calculate the value for the amount values (the y axis of the graph)
function Calculate(){
	amount[0]=currentRetirement.GetValue();//Setup inital condition with seed money
	//Iterate over the list using n-1 to calculate n based upon provided conditions
	for(var i=1;i<=goalAge.GetValue()-age.GetValue();i++){
		amount[i]=amount[i-1]*(averageMarketReturnPercentage.GetValue()/100)+amount[i-1]+additionalPerYear.GetValue();
	}
	//cap x on either end
	if(x>=goalAge.GetValue()-age.GetValue())
		x=goalAge.GetValue()-age.GetValue();
	else if(x<0)
		x=0;
	cursor=amount[x];
}
//paint pretty pictures
function Render(){
	ctx.beginPath();
	ctx.rect(0,0,410,480); ctx.fillStyle='rgba(200,200,200,1)'; ctx.fill();

	ctx.font="12px Time New Roman";
	ctx.fillStyle="black";
	ctx.lineWidth=1;
	ctx.textAlign="left";

	//The Graph
	ctx.fillStyle='rgb(245,245,245)';
	ctx.beginPath();
	ctx.rect(GRAPHLEFT,GRAPHTOP,GRAPHWIDTH,GRAPHHEIGHT);
	ctx.fill();

	//Ages
	ctx.fillStyle='black';
	ctx.fillText(age.GetValue(),GRAPHLEFT-6,GRAPHBOTTOM+12);
	ctx.fillText(Math.floor((goalAge.GetValue()-age.GetValue())/2+age.GetValue()),GRAPHWIDTH/2+GRAPHLEFT-6,GRAPHBOTTOM+12);
	ctx.fillText(goalAge.GetValue(),GRAPHRIGHT-6,GRAPHBOTTOM+12);

	//Returns
	ctx.fillText("$"+parseFloat(Math.round(amount[0]).toPrecision(3)).toLocaleString(),GRAPHLEFT-VisualLength("$"+parseFloat(Math.round(amount[goalAge.GetValue()-age.GetValue()]).toPrecision(3)).toLocaleString(),"Times New Roman",12)-6,GRAPHBOTTOM-4);
	ctx.fillText("$"+parseFloat(Math.round(amount[goalAge.GetValue()-age.GetValue()]/4).toPrecision(3)).toLocaleString(),GRAPHLEFT-VisualLength("$"+parseFloat(Math.round(amount[goalAge.GetValue()-age.GetValue()]).toPrecision(3)).toLocaleString(),"Times New Roman",12)-6,GRAPHHEIGHT/4*3+GRAPHTOP+4);
	ctx.fillText("$"+parseFloat(Math.round(amount[goalAge.GetValue()-age.GetValue()]/4*2).toPrecision(3)).toLocaleString(),GRAPHLEFT-VisualLength("$"+parseFloat(Math.round(amount[goalAge.GetValue()-age.GetValue()]).toPrecision(3)).toLocaleString(),"Times New Roman",12)-6,GRAPHHEIGHT/4*2+GRAPHTOP+4);
	ctx.fillText("$"+parseFloat(Math.round(amount[goalAge.GetValue()-age.GetValue()]/4*3).toPrecision(3)).toLocaleString(),GRAPHLEFT-VisualLength("$"+parseFloat(Math.round(amount[goalAge.GetValue()-age.GetValue()]).toPrecision(3)).toLocaleString(),"Times New Roman",12)-6,GRAPHHEIGHT/4+GRAPHTOP+4);
	ctx.fillText("$"+parseFloat(Math.round(amount[goalAge.GetValue()-age.GetValue()]).toPrecision(3)).toLocaleString(),GRAPHLEFT-VisualLength("$"+parseFloat(Math.round(amount[goalAge.GetValue()-age.GetValue()]).toPrecision(3)).toLocaleString(),"Times New Roman",12)-6,GRAPHTOP+4);
	
	//Vertical Lines
	ctx.beginPath();
	ctx.strokeStyle='black';
	ctx.setLineDash([2,2]);
	ctx.moveTo(GRAPHLEFT,GRAPHBOTTOM);
	ctx.lineTo(GRAPHLEFT,GRAPHTOP);
	ctx.moveTo(GRAPHLEFT+(GRAPHWIDTH)/4,GRAPHBOTTOM);
	ctx.lineTo(GRAPHLEFT+(GRAPHWIDTH)/4,GRAPHTOP);
	ctx.moveTo(GRAPHLEFT+(GRAPHWIDTH)/4*2,GRAPHBOTTOM);
	ctx.lineTo(GRAPHLEFT+(GRAPHWIDTH)/4*2,GRAPHTOP);
	ctx.moveTo(GRAPHLEFT+(GRAPHWIDTH)/4*3,GRAPHBOTTOM);
	ctx.lineTo(GRAPHLEFT+(GRAPHWIDTH)/4*3,GRAPHTOP);
	ctx.moveTo(GRAPHRIGHT,GRAPHBOTTOM);
	ctx.lineTo(GRAPHRIGHT,GRAPHTOP);

	//Horizontal Lines
	ctx.moveTo(GRAPHLEFT,GRAPHTOP);
	ctx.lineTo(GRAPHRIGHT,GRAPHTOP);
	ctx.moveTo(GRAPHLEFT,GRAPHTOP+(GRAPHHEIGHT)/4);
	ctx.lineTo(GRAPHRIGHT,GRAPHTOP+(GRAPHHEIGHT)/4);
	ctx.moveTo(GRAPHLEFT,GRAPHTOP+(GRAPHHEIGHT)/4*2);
	ctx.lineTo(GRAPHRIGHT,GRAPHTOP+(GRAPHHEIGHT)/4*2);
	ctx.moveTo(GRAPHLEFT,GRAPHTOP+(GRAPHHEIGHT)/4*3);
	ctx.lineTo(GRAPHRIGHT,GRAPHTOP+(GRAPHHEIGHT)/4*3);
	ctx.moveTo(GRAPHLEFT,GRAPHBOTTOM);
	ctx.lineTo(GRAPHRIGHT,GRAPHBOTTOM);
	ctx.stroke();

	//Render an informational box that points out the age and predicted value of investment at point under mouse cursor
	ctx.setLineDash([]);
	ctx.beginPath();
	if(x==0){//This is for the end case of x being 0
		//render shaded triangle that points to point on charted line
		ctx.beginPath()
		ctx.fillStyle="rgba(0,0,0,.3)";
		ctx.moveTo(GRAPHLEFT+32,GRAPHTOP+GRAPHHEIGHT/6);
		ctx.lineTo(GRAPHLEFT,GRAPHBOTTOM-GRAPHHEIGHT*(amount[0]/amount[goalAge.GetValue()-age.GetValue()]));
		ctx.lineTo(GRAPHLEFT+GRAPHWIDTH/2,GRAPHTOP+32);
		ctx.fill();
		//Render box for informational text
		ctx.beginPath();
		ctx.fillStyle="rgba(240,240,240,1)";
		ctx.rect(GRAPHLEFT+32,GRAPHTOP+32,GRAPHWIDTH/2-32,GRAPHHEIGHT/6-32);
		ctx.fill();
		ctx.fillStyle="black";
		//And draw that information text
		ctx.fillText("Age:"+(x+age.GetValue())+" -- $"+Math.round(cursor).toLocaleString(),GRAPHLEFT+36,GRAPHTOP+GRAPHHEIGHT/12+20);

		//must move this to this point so the rendering of the curve doesnt get screwed up
		ctx.moveTo(GRAPHLEFT+-1*(GRAPHWIDTH/(goalAge.GetValue()-age.GetValue())),GRAPHBOTTOM-GRAPHHEIGHT*(amount[0]/amount[goalAge.GetValue()-age.GetValue()]));
	}
	//See above comments for indepth explanation
	ctx.moveTo(GRAPHLEFT+0*(GRAPHWIDTH/(goalAge.GetValue()-age.GetValue())),GRAPHBOTTOM-GRAPHHEIGHT*(amount[0]/amount[goalAge.GetValue()-age.GetValue()]));
	for(var i=1;i<=goalAge.GetValue()-age.GetValue();i++){
		if(i==x){
			//shaded triangle
			ctx.beginPath();
			ctx.fillStyle="rgba(0,0,0,.3)";
			ctx.moveTo(GRAPHLEFT+32,GRAPHTOP+GRAPHHEIGHT/6);
			ctx.lineTo(GRAPHLEFT+i*(GRAPHWIDTH/(goalAge.GetValue()-age.GetValue())),GRAPHBOTTOM-GRAPHHEIGHT*(amount[i]/amount[goalAge.GetValue()-age.GetValue()]));
			ctx.lineTo(GRAPHLEFT+GRAPHWIDTH/2,GRAPHTOP+32);
			ctx.fill();
			
			//draw a circle at the point pointed to
			ctx.beginPath();
			ctx.arc(GRAPHLEFT+i*(GRAPHWIDTH/(goalAge.GetValue()-age.GetValue())),GRAPHBOTTOM-GRAPHHEIGHT*(amount[i]/amount[goalAge.GetValue()-age.GetValue()]),8,0,2*Math.PI)
			ctx.stroke();

			//draw the box
			ctx.beginPath();
			ctx.fillStyle="rgba(240,240,240,1)";
			ctx.rect(GRAPHLEFT+32,GRAPHTOP+32,GRAPHWIDTH/2-32,GRAPHHEIGHT/6-32);
			ctx.fill();
			ctx.fillStyle="black";
			//text
			ctx.fillText("Age:"+(x+age.GetValue())+" -- $"+Math.round(cursor).toLocaleString(),GRAPHLEFT+36,GRAPHTOP+GRAPHHEIGHT/12+20);

			//must move this to this point so the rendering of the curve doesnt get screwed up
			ctx.moveTo(GRAPHLEFT+(i-1)*(GRAPHWIDTH/(goalAge.GetValue()-age.GetValue())),GRAPHBOTTOM-GRAPHHEIGHT*(amount[i-1]/amount[goalAge.GetValue()-age.GetValue()]));
		}
		//start sketching lines for the curve
		ctx.lineTo(GRAPHLEFT+i*(GRAPHWIDTH/(goalAge.GetValue()-age.GetValue())),GRAPHBOTTOM-GRAPHHEIGHT*(amount[i]/amount[goalAge.GetValue()-age.GetValue()]));
		ctx.stroke();
	}
	document.getElementById("result").innerHTML="$"+Math.round(amount[goalAge.GetValue()-age.GetValue()]).toLocaleString();
}
//Basic program loop to calcualte and render. If not continuously called, but only called when something changes.
function ProgramLoop(){
	Calculate();
	Render();
}
ProgramLoop();
</script>
</html>
