<?PHP
header('Access-Control-Allow-Origin: *');
?>
<html lang="en">
<head>
<meta http-equiv="X-UA-Compatible" content="ie=edge"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<title>Schedule</title>
<style>
.date { width:100% ;
        height="150px" ;
        justify-content : center ;
        align-items : center ;
        padding : 15px ; 
        background : black ;
        color : white}
#schedule {display:flex }
#schedule_time { flex:33%} 
#slot  { flex:33%}   
#item_date { flex:33%} 
</style>
</head>
<body>

    
    <div class="date">
    <label>Enter Item Date : &nbsp</label>
    <input type="date" id="chkdate"><button onclick="chkschedule()">Check</button><input type="submit" values="Submit" onclick="chkschedule()"></div>
    <canvas id="order_schedule_graph" style="width:100%;max-width:600px;border:2px solid black"></canvas></br>
    <canvas id="graph" style="width:100%;max-width:600px;border:2px solid black"></canvas>
    <span id="demoo" width="50%"></span>
    <canvas id="days_prior_graph" style="width:100%;max-width:600px;border:2px solid black"></canvas>
    <br><hr></hr>
    <div id="schedule">
    <div id="schedule_time">Schedule Time</div>
    <div id="slot" >Schedule Slot</div>
    <div id="item_date" >Item date</div>
    </div>

<script>

    //retreiving data from json file
    var i=0,size=0,j=0;
	const json_url = 'schedule.json';
    let schedule_date= [],schedule_time= [],slot= [],item_date= [];
     async function retreivedata() {
	const response = await fetch(json_url);
	const data =await response.json();
    size = data.length;
    while(i<data.length) {
        schedule_date.push(String(data[i].schedule_time.split(" ")[0]));
        schedule_time.push(String(data[i].schedule_time.split(" ")[1]));
        slot.push(data[i].slot);
        item_date.push(String(data[i].item_date));
      //  document.getElementById("schedule_time").innerHTML+= "</br>" +data[i].schedule_time;
        //document.getElementById("slot").innerHTML+= "</br>"+data[i].slot;        
        //document.getElementById("item_date").innerHTML+= "</br>" +data[i].item_date;
        i++;
 
    console.log(data[i]);   //print data in console
     }
   }
retreivedata();              // calling datareterive function

    function chkschedule() {
        
        var xvaluedate= [],xvaluetime= [],xvalueslot= [],yvalue= [],xvalue= [],xcount= [],ycount= [];
        var chkdate=String(document.getElementById("chkdate").value);
        while(j<size) {
            if(chkdate==item_date[j]) {
                xvaluedate.push(schedule_date[j]);
                xvaluetime.push(schedule_time[j]);
                xvalueslot.push(slot[j]);


            }
        j++;  
        }
        const count = {};
        var k=0,index={};
for (var element of xvaluedate) {
  if (count[element]) {
    count[element] += 1;
  } else {
    count[element] = 1;
    
  }
console.log(element);
}
console.log(index);
var counttime = {} , phase = [0,0,0,0,0];
for (var prop in count) {
    xvalue.push(prop);
    yvalue.push(count[prop]);
  console.log(`count.${prop} = ${count[prop]}`);
var morning,night,afternoon,evening;
 for(var m=0;m<xvaluedate.length;m++) {
 if(xvaluedate[m]==prop) {
    if(xvaluetime[m].split(":")[0]>=09 && xvaluetime[m].split(":")[0]<12 )
    phase[0]++ ;
    else if(xvaluetime[m].split(":")[0]>=12 && xvaluetime[m].split(":")[0]<15 )
    phase[1] +=1 ;
    else if(xvaluetime[m].split(":")[0]>=15 && xvaluetime[m].split(":")[0]<18 )
    phase[2] +=1 ;
    else if(xvaluetime[m].split(":")[0]>=18 && xvaluetime[m].split(":")[0]<21 )
    phase[3] +=1 ;
    else 
    phase[4] +=1;

}
}
counttime[prop] =phase;
console.log(counttime[prop]);
phase = [0,0,0,0,0];
  }
  console.log(counttime);
  for(var n in counttime ) {
    
      ycount.push(counttime[n]);
  }
var arr=counttime['2022-01-09'];
console.log(counttime['2022-01-09']);


//graph to display order scheduled at what time of day

var colors = ["red", "green","blue","orange","brown","yellow","grey","purple"];
var c=0;
console.log(colors);
new Chart("time_graph", {
  type: "line",
  data: {
   // labels: ['09:00AM to 12:00PM','12:00PM to 03:00PM','03:00PM to 06:00PM','06:00PM to 09:00PM'],
 //  labels: ['Morning','Afternoon','Evening','Night'],
    labels: arr,
    datasets: [
       { 
      data: [5,6,9,0,5],
      borderColor: "green",
      fill: false
    },
    { 
      data: [5,6,8,7,4],
      borderColor: "red",
      fill: false
    },
        
    ]
    },
  options: {
    legend: {display: false}
  }
});

//Bar graph to show dates on which order was scheduled

new Chart("order_schedule_graph", {
  type: "bar",
  data: {
    labels: xvalue,
    datasets: [{
      backgroundColor: colors,
      data: yvalue
    }]
  },
  options: {
    legend: {display: false},
    title: {
      display: true,
      text: "Orders Scheduled for "+chkdate
    }
  }
});
var sum=0;
for(var l in yvalue) {
    sum +=parseInt(yvalue[l]);
}
var xday= [],ypercentage= [];
for(var l in xvalue) {
    var day=parseInt(chkdate.split("-")[2])-parseInt(xvalue[l].split("-")[2]);
    var percentage=(parseInt(yvalue[l])*100)/55;
    document.getElementById("demoo").innerHTML+="</br> &nbsp"+parseInt(percentage)+" % of schedueling done "+day+" days prior.";
    xday.push(day+" days");
    ypercentage.push(parseInt(percentage));
}
console.log(xday);
console.log(ypercentage);

// pie cart for no. of days prior order was scheduled

new Chart("days_prior_graph", {
  type: "pie",
  data: {
    labels: xday,
    datasets: [{
      backgroundColor: colors,
      data: ypercentage
    }]
  },
  options: {
    title: {
      display: true,
      text: "Prior Scheduling Chart"
    }
  }
});

    }

</script>
</body>
</html>
