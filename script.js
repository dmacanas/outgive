var imageIndex=1;

var galleryFolder = "school_logos/";

var gallerySize=12;

function shiftLeft()
{
    
	if(imageIndex==(gallerySize))
        {
            populateWithBlank2R(imageIndex-2);
            return;
	}
        else if(imageIndex==(gallerySize-1))
        {
            imageIndex++;
            populateWithBlank1R(imageIndex-2);
        }
        else if(imageIndex==1)
        {
            imageIndex++;
            populateWithBlank1L(imageIndex-1);
        }
	else
        {
	    imageIndex++;
	    populateThumbsWithIndex(imageIndex-3);
	}
}  

function shiftRight()
{
	if(imageIndex==1)
        {
            populateWithBlank2L(imageIndex);
	    return;
        }
        else if(imageIndex==2)
        {
            imageIndex--;
            populateWithBlank2L(imageIndex);
        }
        else if(imageIndex==3)
        {
            imageIndex--;
            populateWithBlank1L(imageIndex-1);
        }
        else if(imageIndex==gallerySize)
        {
            imageIndex--;
            populateWithBlank1R(imageIndex-2)
        }
	else
        {
            imageIndex--;
            populateThumbsWithIndex(imageIndex-2);
	}
}

function populateWithBlank2L(index)
{
    document.getElementById("logo"+1).src=galleryFolder+"blank"+".png";
    document.getElementById("logo"+2).src=galleryFolder+"blank"+".png";
    document.getElementById("logo"+3).src=galleryFolder+"logo"+index+".jpeg";
    document.getElementById("logo"+4).src=galleryFolder+"logo"+(index+1)+".jpeg";
    document.getElementById("logo"+5).src=galleryFolder+"logo"+(index+2)+".jpeg";
}
function populateWithBlank1L(index)
{
    document.getElementById("logo"+1).src=galleryFolder+"blank"+".png";
    document.getElementById("logo"+2).src=galleryFolder+"logo"+index+".jpeg";
    document.getElementById("logo"+3).src=galleryFolder+"logo"+(index+1)+".jpeg";
    document.getElementById("logo"+4).src=galleryFolder+"logo"+(index+2)+".jpeg";
    document.getElementById("logo"+5).src=galleryFolder+"logo"+(index+3)+".jpeg";
}
function populateWithBlank2R(index)
{
    document.getElementById("logo"+1).src=galleryFolder+"logo"+index+".jpeg";
    document.getElementById("logo"+2).src=galleryFolder+"logo"+(index+1)+".jpeg";
    document.getElementById("logo"+3).src=galleryFolder+"logo"+(index+2)+".jpeg";
    document.getElementById("logo"+4).src=galleryFolder+"blank"+".png";
    document.getElementById("logo"+5).src=galleryFolder+"blank"+".png";
}
function populateWithBlank1R(index)
{
    document.getElementById("logo"+1).src=galleryFolder+"logo"+index+".jpeg";
    document.getElementById("logo"+2).src=galleryFolder+"logo"+(index+1)+".jpeg";
    document.getElementById("logo"+3).src=galleryFolder+"logo"+(index+2)+".jpeg";
    document.getElementById("logo"+4).src=galleryFolder+"logo"+(index+3)+".jpeg";
    document.getElementById("logo"+5).src=galleryFolder+"blank"+".png";
}

  
function populateThumbsWithIndex (index)
  {
     document.getElementById("logo"+1).src=galleryFolder+"logo"+index+".jpeg";
     document.getElementById("logo"+2).src=galleryFolder+"logo"+(index+1)+".jpeg";
     document.getElementById("logo"+3).src=galleryFolder+"logo"+(index+2)+".jpeg";
     document.getElementById("logo"+4).src=galleryFolder+"logo"+(index+3)+".jpeg";
     document.getElementById("logo"+5).src=galleryFolder+"logo"+(index+4)+".jpeg";
  }
  
var clicked = 0;  

function change_selection_1(school,module)
{
	if(clicked == 0)
	{
		document.getElementById(school).style.backgroundColor="#918337";
		document.getElementById(school).style.marginLeft="15px";
		document.getElementById("display_info").style.visibility="hidden";
		document.getElementById("redirect").style.visibility="hidden";
		document.getElementById(module).style.visibility="visible";
		document.getElementById("user_content").style.height="450px";
		document.getElementById("pledge1").innerHTML="$" + pledge_amount;
		document.getElementById("user_pledge").innerHTML="$" + pledge_total;
		document.getElementById("total_school").innerHTML="$" + pledge_total;
		document.getElementById("outgive").innerHTML="$" + pledge_total;
		clicked =1;
	}
	else
	{
		document.getElementById(school).style.backgroundColor="#385997";
		document.getElementById(school).style.marginLeft="-10px";
		document.getElementById("display_info").style.visibility="visible";
		document.getElementById(module).style.visibility="hidden";
		document.getElementById("redirect").style.visibility="hidden";
		document.getElementById("user_content").style.height="300px";
		clicked =0;
	}
}

var pledge_amount=0;
var pledge_total=0;
function pledge_redirect()
{
	var amount_temp;
	var total_temp;
	pledge_amount=prompt("Please enter your pledge amount (numerical Value)","25");
	document.getElementById("pledge_value").innerHTML="Thank you for making a $" + pledge_amount + " pledge.";
	document.getElementById("redirect").style.visibility="visible";
	document.getElementById("university1_info").style.visibility="hidden";
	document.getElementById("display_info").style.visibility="hidden";
	var t=setTimeout("return_home()",5000);
	amount_temp = parseInt(pledge_amount);
	total_temp = parseInt(pledge_total);
	pledge_total = total_temp + amount_temp;
	document.getElementById("pledge1").innerHTML="$" + pledge_amount;
	document.getElementById("user_pledge").innerHTML="$" + pledge_total;
	document.getElementById("total_school").innerHTML="$" + pledge_total;
	document.getElementById("outgive").innerHTML="$" + pledge_total;
}


function return_home()
{
	document.getElementById("redirect").style.visibility="hidden";
	document.getElementById("university1_info").style.visibility="visible";
	document.getElementById("display_info").style.visibility="hidden";
}



