<!--  Kate McCarthy
      01/2021
      MedSched Page 2 (medication schedule) -->

<!DOCTYPE HTML>
<html>
<head>
<style>
</style>
<link rel="stylesheet" href="schedule.css" />
</head>
<body onload="onOpen()">

  <?php
  // define variables and set to empty values

  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST["name"];
    $dose = $_POST["dose"];
    $units = $_POST["units"];
    $hr = $_POST["hr"];
    $min = $_POST["min"];

    $info = $_POST["info"];

    $numDoses = $_POST["numDoses"];

    $numMeds = $_POST["numMeds"];

    function test_input($data) {
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      return $data;
    }
  }
  ?>

<!-- MEDICATION SCHEDULE -->

<h2>Medication Schedule</h2>
<table id="medSched">
  <body>  <!-- list events chronologically -->

  </body>
</table>


<?php
$colors = array();
$colorList = array("earth", "saturn", "mercury", "venus", "mars", "jupiter", "sun", "nebula");
$eventTitles = array(); //String with medication name, dose, and units
$hrs = array();
$hrIds = array();
$sortHrs = array();
$sortHrIds = array();
$firstHour = 8;
$mins = array();
$colSpans = array(); //column width of each dose

for ($x = 0; $x <= $numMeds; $x++) {
  if ( !( empty($info[$x]) ) ) { // to account for any removed medications

    // assign the same color to all doses of each medication
    if ($x < count($colorList)) {
      $color = $colorList[$x];
    }
    else {
      $color = 'random';
      // ASSIGN RANDOM COLOR (in js)
    }

    $doseLim = $info[$x]['numDoses'];
    for ($d = 0; $d < $doseLim; $d++) {
      $hrId = $x . 'hr' . $d;
      $minId = 'min' . $d;
      if ( empty($info[$x][$hrId]) ){
        if ( empty($info[$x][$minId]) ) {
          $doseLim = $doseLim + 1; // to account for any dose removals
        }
      } else {
        if ( empty($info[$x][$minId]) ) {
          $mins[] = '00'; // default minute value if only the hour was entered.
        } else {
          if ((int)$info[$x][$minId] < 10) {
            $mins[] = '0' . (int)$info[$x][$minId];
          } else {
            $mins[] = $info[$x][$minId];
          }
        }
        $hrs[] = $info[$x][$hrId];
        $hrIds[] = $hrId;
        $sortHrs[] = $info[$x][$hrId];
        $eventTitle = $info[$x]['name'] . ' (' . $info[$x]['dose'] . ' ' . $info[$x]['units'] . ')';
        $colors[] = $color;
        $eventTitles[] = $eventTitle;
      }
    }
  }
}

  $counts = array_count_values($hrs);
  sort($sortHrs);
  $usedNums = array();

  for ($k = 0; $k < count($hrs); $k++) {
    $numMatch = $counts[$hrs[$k]];
    $colSpans[] = 4 / $numMatch;

    // set up a universal index by matching ids to sorted hrs
    for ($m = 0; $m < count($hrs); $m++) {
        if ($sortHrs[$k] == $hrs[$m]) {
          if (in_array($hrIds[$m], $usedNums)) {
            //ignore it
          } else {
          //if ($foundHrs == 0) {
            $sortHrIds[] = $hrIds[$m];
            //$foundHrs = $foundHrs + 1;
            $usedNums[] = $hrIds[$m];
          }
        }
    }
  }
  //print_r($sortHrIds);

  if ( !empty($sortHrs) ) $firstHour = $sortHrs[0];
  print_r($firstHour);

?>


<script>


function onOpen() {

  var table = document.getElementById("medSched");
  if ( "<?php echo (!empty($info)); ?>") {
  var sortHrs = <?php echo '["' . implode('", "', $sortHrs) . '"]' ?>;
  var hrIds = <?php echo '["' . implode('", "', $hrIds) . '"]' ?>;
  var sortHrIds = <?php echo '["' . implode('", "', $sortHrIds) . '"]' ?>;
  var colors = <?php echo '["' . implode('", "', $colors) . '"]' ?>;
  var colSpans = <?php echo '["' . implode('", "', $colSpans) . '"]' ?>;
  var eventTitles = <?php echo '["' . implode('", "', $eventTitles) . '"]' ?>;
  var hrs = <?php echo '["' . implode('", "', $hrs) . '"]' ?>;
  var mins = <?php echo '["' . implode('", "', $mins) . '"]' ?>;

  // first hour label on schedule
  var minStart = 10;
  var startDay = "<?php echo $firstHour; ?>";
  if (startDay > minStart) startDay = minStart;

  //var lastHr = 23;

  for (var h = sortHrs.length - 1; h >= 0; h--) {
    var i = hrIds.indexOf(sortHrIds[h]);
    var hr = hrs[i];
    var color = colors[i];
    var eventTitle = eventTitles[i];
    var colSpan = colSpans[i];

    var min = parseInt((mins[i]).toString(), 10);
    if (min < 10) min = ':0' + min;
    else min = ':' + min;

    var halfOfDay = "";
    var hr = parseInt(hr.toString(), 10); // h = hour of dose in 24-hour clock format
    if (hr == startDay && hr < 12) halfOfDay = "<b>a.m.</b>";
    else if (hr == 12 || (hr > 12 && hr == startDay)) halfOfDay = "<b>p.m.</b>";
    else halfOfDay = ":00";

    var ampm = "";
    if (hr < 12) ampm = "a.m.";
    else ampm = "p.m.";

    var g = hr; // g = hour of dose in 12-hour clock format
    if (hr == 0) {
      g = 12;
    } else if (hr > 12) {
      g = hr - 12;
    }

    if (h != sortHrs.length - 1) {
    if (sortHrs[h] != sortHrs[h+1])  {

        var row = table.insertRow(0);
        row.id = hr.toString();
        var th = document.createElement('th');
        th.innerHTML = "" + g.toString() + halfOfDay;
        th.style.padding = "0.5rem 1rem";
        row.appendChild(th);

        var cell = row.insertCell(1);
        cell.setAttribute('rowspan', '1');
        cell.setAttribute('class', ('stage-' + color));
        cell.setAttribute('colspan', colSpan);
        cell.innerHTML = eventTitle + '<br />' + g.toString() + min + ampm;

        var row1 = table.insertRow(1);
        var th1 = document.createElement('th');
        th1.style.padding = "0.5rem 1rem";
        th1.innerHTML = "" + g.toString() + ":30";
        row1.appendChild(th1);

        if (h > 0) {
          var timeDif = hr - parseInt(sortHrs[h-1].toString(), 10);
          if (timeDif > 1) {
            //add in hour and hour:30 labels
            for (var k = 1; k < timeDif; k++) {

              var hour = hr - k;

              if (hour == startDay && hour < 12) halfOfDay = "<b>a.m.</b>";
              else if (hour == 12 || (hour > 12 && hour == startDay)) halfOfDay = "<b>p.m.</b>";
              else halfOfDay = ":00";

              var clock = hour; // g = hour of dose in 12-hour clock format
              if (hour == 0) clock = 12;
              else if (hour > 12) clock = hour - 12;

              var row = table.insertRow(0);
              row.id = hour.toString();
              var th = document.createElement('th');
              th.innerHTML = "" + clock.toString() + halfOfDay;
              th.style.padding = "0.5rem 1rem";
              row.appendChild(th);

              var row1 = table.insertRow(1);
              var th1 = document.createElement('th');
              th1.style.padding = "0.5rem 1rem";
              th1.innerHTML = "" + clock.toString() + ":30";
              row1.appendChild(th1);
            }
          }
        }
      } // h (index) > 0hr is first instance of itself in the sortHrs array
      else {

        var row = document.getElementById(hr.toString());
        var cell = row.insertCell(1);
        cell.setAttribute('rowspan', '1');
        cell.setAttribute('class', ('stage-' + color));
        cell.setAttribute('colspan', colSpan);
        cell.innerHTML = eventTitle + '<br />' + g.toString() + min + ampm;

      }
    } else {

      var row = table.insertRow(0);
      row.id = hr.toString();
      var th = document.createElement('th');
      th.innerHTML = "" + g.toString() + halfOfDay;
      th.style.padding = "0.5rem 1rem";
      row.appendChild(th);

      var cell = row.insertCell(1);
      cell.setAttribute('rowspan', '1');
      cell.setAttribute('class', ('stage-' + color));
      cell.setAttribute('colspan', colSpan);
      cell.innerHTML = eventTitle + '<br />' + g.toString() + min + ampm;

      var row1 = table.insertRow(1);
      var th1 = document.createElement('th');
      th1.style.padding = "0.5rem 1rem";
      th1.innerHTML = "" + g.toString() + ":30";
      row1.appendChild(th1);

      if (h > 0) {
        var timeDif = hr - parseInt(sortHrs[h-1].toString(), 10);
        if (timeDif > 1) {
          //add in hour and hour:30 labels
          for (var k = 1; k < timeDif; k++) {

            var hour = hr - k;

            if (hour == startDay && hour < 12) halfOfDay = "<b>a.m.</b>";
            else if (hour == 12 || (hour > 12 && hour == startDay)) halfOfDay = "<b>p.m.</b>";
            else halfOfDay = ":00";

            var clock = hour; // g = hour of dose in 12-hour clock format
            if (hour == 0) clock = 12;
            else if (hour > 12) clock = hour - 12;

            var row = table.insertRow(0);
            row.id = hour.toString();
            var th = document.createElement('th');
            th.innerHTML = "" + clock.toString() + halfOfDay;
            th.style.padding = "0.5rem 1rem";
            row.appendChild(th);

            var row1 = table.insertRow(1);
            var th1 = document.createElement('th');
            th1.style.padding = "0.5rem 1rem";
            th1.innerHTML = "" + clock.toString() + ":30";
            row1.appendChild(th1);
          }
        }
      }
    }

    if (h == 0) {
      var timeDif = hr - startDay;
      if (timeDif > 0) {
        //add in hour and hour:30 labels
        for (var k = 1; k < timeDif; k++) {

          var hour = hr - k;

          if (hour == startDay && hour < 12) halfOfDay = "<b>a.m.</b>";
          else if (hour == 12 || (hour > 12 && hour == startDay)) halfOfDay = "<b>p.m.</b>";
          else halfOfDay = ":00";

          var clock = hour; // g = hour of dose in 12-hour clock format
          if (hour == 0) clock = 12;
          else if (hour > 12) clock = hour - 12;

          var row = table.insertRow(0);
          row.id = hour.toString();
          var th = document.createElement('th');
          th.innerHTML = "" + clock.toString() + halfOfDay;
          th.style.padding = "0.5rem 1rem";
          row.appendChild(th);

          var row1 = table.insertRow(1);
          var th1 = document.createElement('th');
          th1.style.padding = "0.5rem 1rem";
          th1.innerHTML = "" + clock.toString() + ":30";
          row1.appendChild(th1);
        }
      }
    }

    /*if (h == 0) {
      var timeDif = hr - startDay;
      if (timeDif > 0) {
        //add in hour and hour:30 labels
        for (var k = 1; k <= timeDif; k++) {

          var hour = hr - k;

          if (hour == startDay && hour < 12) halfOfDay = "<b>a.m.</b>";
          else if (hour == 12 || (hour > 12 && hour == startDay)) halfOfDay = "<b>p.m.</b>";
          else halfOfDay = ":00";

          var clock = hour; // g = hour of dose in 12-hour clock format
          if (hour == 0) clock = 12;
          else if (hour > 12) clock = hour - 12;

          var row = table.insertRow(0);
          row.id = hour.toString();
          var th = document.createElement('th');
          th.innerHTML = "" + clock.toString() + halfOfDay;
          th.style.padding = "0.5rem 1rem";
          row.appendChild(th);

          var row1 = table.insertRow(1);
          var th1 = document.createElement('th');
          th1.style.padding = "0.5rem 1rem";
          th1.innerHTML = "" + clock.toString() + ":30";
          row1.appendChild(th1);
        }
      }
    }*/

   }

  }
}

  //basic med exploration

  //document.getElementById('littleP').innerHTML = '' + passedArray.toString();
  //var med1 = </?php echo '["' . implode('", "', $info[0]) . '"]' ?>;
  //var med2 = </?php echo '["' . implode('", "', $info[1]) . '"]' ?>;
  //var med3 = </?php echo '["' . implode('", "', $info[2]) . '"]' ?>;
  //var med4 = </?php echo '["' . implode('", "', $info[2]) . '"]' ?>;
  /*var meds = new Array();
  meds.push(</?php echo '["' . implode('", "', $info[0]) . '"]' ?>);
  meds.push(</?php echo '["' . implode('", "', $info[1]) . '"]' ?>);

  for (var i in meds) {
    var med = meds[i];
    var numDoses = med[0];
    var name = med[1];
    var dose = med[2];
    var units = med[3];
    var hr = med[4]; //from this point on, it's going to just be specifying dose times.

    var min = parseInt((med[5]).toString(), 10);
    if (min < 10) min = ':0' + min;
    else min = ':' + min;

    document.write('<br /><br />name: ' + name + '<br />dose: ' + dose + ' ' + units + '<br />time: ' + hr + min);
  }

  for (var i in meds) {
    var med = meds[i];
    var numDoses = med[0];
    var name = med[1];
    var dose = med[2];
    var units = med[3];
    var hr = med[4]; //from this point on, it's going to just be specifying dose times.

    var min = parseInt((med[5]).toString(), 10);
    if (min < 10) min = ':0' + min;
    else min = ':' + min;

    document.write('<br /><br />name: ' + name + '<br />dose: ' + dose + ' ' + units + '<br />time: ' + hr + min);
  }*/






/*  function onOpen() {

    var table = document.getElementById("medSched");
    //var hrs = "</?php echo $hrs; ?>";
    var hrs = "</?php echo json_encode($hrs); ?>";
    for(var i=0; i<hrs.length; i++){
        alert(hrs[i]);
    }
    //var hrIds = "</?php echo $hrIds; ?>";
    //var sortHrs = "</?php echo $sortHrs; ?>";
    //var sortHrIds = "</?php echo $sortHrIds; ?>";
    //var eventTitles = "</?php echo $eventTitles; ?>";
    //var mins = "</?php echo $mins; ?>";
    //var colors = "</?php echo $colors; ?>";
    //var colSpans = "</?php echo $colSpans; ?>";

    // first hour label on schedule
    //var startDay = "</?php echo $firstHour; ?>";
    //if (startDay > 8) startDay = 8;
    //var lastHr = 23;

    //for (var i = 0; i < sortHrIds.length; i++) {

      //var index = hrIds.indexOf(sortHrIds[i]);
      var index = 1;

      //var color = colors[index];
      //var eventTitle = eventTitles[index];
      //var min = mins[index];
      //var colSpan = colSpans[index];

      /*var halfOfDay = "";
      var h = 6;
      //var h = parseInt(sortHrs[i].toString(), 10); // h = hour of dose in 24-hour clock format
      if (h == startDay && h < 12) halfOfDay = "<b>a.m.</b>";
      else if (h == 12 || (h > 12 && h == startDay)) halfOfDay = "<b>p.m.</b>";
      else halfOfDay = ":00";

      var g = h; // g = hour of dose in 12-hour clock format
      if (h == 0) {
        g = 12;
      } else if (h > 12) {
        g = h - 12;
      }

      var row = table.insertRow(0);
      var th = document.createElement('th');
      th.innerHTML = "" + 'hi';
      //th.innerHTML = "" + g.toString() + halfOfDay;
      th.style.padding = "0.5rem 1rem";

      row.appendChild(th);

      var cell = row.insertCell(1);
      //cell.id = '' + h.toString() + '-' + i.toString();
      cell.style.display = "block";
      cell.innerHTML = 'time would go here  -->' + hrs[0];
      document.getElementById('littleP').innerHTML = 'little P says the first hour is ' + hrs[0];
      //cell.innerHTML = "" + eventTitle + "<br />" + 'time would go here'; // title of scheduled dose
      cell.setAttribute('rowspan', "1"); // 1 by default
      //cell.setAttribute('class', ("stage-" + color)); // unless color = 'random'
      cell.setAttribute('class', ("stage-earth"));
      block.setAttribute('colspan', 4);
      //block.setAttribute('colspan', colSpan); // width of dose block

      var row = table.insertRow(1);
      var th2 = document.createElement('th');
      th.style.padding = "0.5rem 1rem";
      th.innerHTML = "" + g.toString() + ":30";
      row.appendChild(th2);

    //}
}*/





</script>

</body>
</html>
