<!--  Kate McCarthy
      01/2021
      MedSched Page 1 (medication form)

      MedSched makes tracking the post-op medications of surgical patients a breeze.
      index.php encodes the medication form where patients can schedule each dose.
      When the "Create Schedule" button is clicked, the user is redirected to time.php to view their schedule.
-->

<!DOCTYPE HTML>
<html>
<head>
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;300&display=swap" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="main.css" />
<style>
.error {color: #FF0000;}
.remove {
  color: #ff4942 !important;
  font-family: "Poppins", Arial, Helvetica, sans-serif;
  font-weight: 300;
  text-decoration: none;
  border-radius: 12px;
  padding: 20px, 2px;
  background: #ffffff;
  border: 1px solid #ff4942 !important;
  display: inline-block;
  transition: all 0.4s ease 0s;
}
.remove:hover {
  color: #ffffff !important;
  background-color: #ff4942;
  box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.1);
  background: #ff4942;
  border-color: #ff4942 !important;
  transition: all 0.4s ease 0s;
}

.add {
  color: #1dc25c !important;
  font-family: "Poppins", Arial, Helvetica, sans-serif;
  font-weight: 300;
  text-decoration: none;
  border-radius: 12px;
  padding: 20px, 2px;
  background: #ffffff;
  border: 1px solid #1dc25c !important;
  display: inline-block;
  transition: all 0.4s ease 0s;
}
.add:hover {
  color: #ffffff !important;
  background-color: #1dc25c;
  box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.1);
  background: #1dc25c;
  border-color: #1dc25c !important;
  transition: all 0.4s ease 0s;
}

.submit {
  border: none;
  background: #39c4c2;
  font-family: "Poppins", Arial, Helvetica, sans-serif;
  font-weight: 300;
  font-size: 18px;
  color: #ffffff !important;
  padding: 20px;
  border-radius: 6px;
  display: inline-block;
  transition: all 0.3s ease 0s;
}
.submit:hover {
  color: #404040 !important;
  font-weight: 700 !important;
  letter-spacing: 3px;
  background: none;
  -webkit-box-shadow: 0px 5px 40px -10px rgba(0,0,0,0.57);
  -moz-box-shadow: 0px 5px 40px -10px rgba(0,0,0,0.57);
  transition: all 0.3s ease 0s;
}

</style>
</head>
<body onload="onOpen()">


<h2>Post-Op Medication Schedule</h2>
<!--<p><span class="error">* required field</span></p>-->
<form method="post" action="time.php">

  <div id="allMeds">

    <!-- only one medication is visible upon page load, until the user clicks "Add A Medication" -->
    <div id='med0' class='med'>

      <div class='row'>

      <!-- hidden variable to count number of doses -->
      <input type='hidden' id='secret0' name='info[0][numDoses]' value='1'>

      <!-- name of the medication -->
      <p>Medication Name:<input type='text' name='info[0][name]'></p>
      <br>

      <!-- quantity and unit of a dose -->
      <p>Dose: <input type='number' name='info[0][dose]'>   Units: <input type='text' name='info[0][units]' min='1'></p>
      <br><br>

        <div id='doses0'>

          <!-- the first scheduled dose of the medication -->
          <div id='0dose0' class='time'>

            <!-- button to remove dose -->
            <p>Dose &emsp; <input type='button' class = 'remove' value='Remove Dose' onclick='removeDose(0,0)'></p>

            <!-- hour of scheduled dose -->
            <label>Hour (24-Hour Clock)</label><input type='number' name='info[0][0hr0]' min='00' max='23' placeholder='00'>

            <!-- minute of scheduled dose -->
            <label>Minute</label><input type='number' name='info[0][min0]' min='00' max='59' placeholder='00'>

          </div>
        </div>
        <br><br>

        <!-- button to schedule additional times to take  medication 0 by calling addDose(0) -->
        <input type='button' class='add' value='Schedule Another Dose' onclick='addDose(0)'>

      <br><br>
      </div>
      <br><br>
    </div>
    <br><br>
  </div>

  <div class="row">
  <br>
  <!-- button to add unlimited additional medications by calling addMedication() -->
  <input type="button" class='add' name="add" value="Add A Medication" onclick="addMedication()">
  <br><br>
  </div>

  <br><br>

  <div class="row">
  <br>

  <!-- hidden variable that tracks the total number of medications -->
  <input type='hidden' id='countMeds' name='numMeds' value='1'>

  <!-- button to submit the form and redirect to time.php -->
  <input type="submit" class="submit" name="submit" value="Create Schedule">

  <br><br>
  </div>

</form>


<script type='text/javascript'>

  var i = 0;
  var doses = new Array();

  function onOpen() {
    doses.push(0);
  }

  /*
   * Inserts another medication with identical input fields and buttons at the bottom of the form.
   */
  function addMedication() {

    i = i + 1;

    var increment = parseInt(document.getElementById('countMeds').value, 10) + 1;
    document.getElementById('countMeds').setAttribute('value', increment);

    var allMeds = document.getElementById('allMeds');

    var newMed = document.createElement('div');
    newMed.id = 'med' + i;

    var line1 = "<div class='row'><input type='hidden' id='secret" + i + "' name='info[" + i + "][numDoses]' value='1'><br><input type='button' class = 'remove' value='Remove Medication' onclick='removeMed(" + i + ")'><br><p>Medication Name:<input type='text' name='info[" + i + "][name]'></p><br><p>Dose: <input type='number' name='info[" + i + "][dose]'>   Units: <input type='text' name='info[" + i + "][units]' min='1'></p><br><br>";
    var line2 = "<div id='doses" + i + "'><div id='" + i + "dose0' class='time'><p>Dose &emsp; <input type='button' class = 'remove' value='Remove Dose' onclick='removeDose(" + i + ",0)'></p><br><label>Hour (24-Hour Clock)</label><input type='number' name='info[" + i + "][" + i + "hr0]' min='01' max='24' placeholder='00'>";
    var line3 = "<label>Minute</label><input type='number' name='info[" + i + "][min0]' min='00' max='59' placeholder='00'></div><br><br></div>";
    var line4 = "<input type='button' class='add' value='Schedule Another Dose' onclick='addDose(" + i + ")'><br><br></div><br><br>";

    newMed.innerHTML = line1 + line2 + line3 + line4;

    doses.push(0);

    allMeds.appendChild(newMed);

  }

  /*
   * @param num   the ID of the selected medication
   *
   * Inserts another dose with identical input fields and buttons below the previous dose.
   */
  function addDose(num) {
    var newDose = document.createElement('div');
    var numDoses;
    doses[num] = doses[num] + 1;
    var numDoses = doses[num];
    newDose.id = num + 'dose' + numDoses;

    var hr = "<p>Dose &emsp; <input type='button' class = 'remove' value='Remove Dose' onclick='removeDose(" + num + "," + numDoses + ")'></p><label>Hour (24-Hour Clock)</label><input type='number' name='info[" + num + "][" + num + "hr" + numDoses + "]' min='01' max='24' placeholder='00'>";
    var min = "<label>Minute</label><input type='number' name='info[" + num + "][min" + numDoses + "]' min='00' max='59' placeholder='00'><br><br>";

    newDose.innerHTML = hr + min;

    var secretId = 'secret' + num;
    incrementDose = parseInt(document.getElementById(secretId).value, 10) + 1;
    document.getElementById(secretId).setAttribute('value', incrementDose);

    document.getElementById('doses' + num).appendChild(newDose);
  }

  /*
   * @param nMed  the ID of the selected medication
   * @param nDose the ID of the selected dose
   *
   * Removes the selected dose from the medication; any doses below the
   * removed dose shift up to maintain consistent formatting.
   */
  function removeDose(nMed, nDose) { // remove a scheduled dose from a medication
    targetDose = document.getElementById(nMed + 'dose' + nDose);
    targetDose.parentNode.removeChild(targetDose);

    var secretId = 'secret' + nMed;
    decrementDose = parseInt(document.getElementById(secretId).value, 10) - 1;
    document.getElementById(secretId).setAttribute('value', decrementDose);
  }

  /*
   * @param nMed  the ID of the selected medication
   *
   * Removes the selected medication from the form; any medications below
   * the removed one shift up to maintain consistent formatting.
   */
  function removeMed(nMed) { // remove a medication
    targetMed = document.getElementById('med' + nMed);
    targetMed.parentNode.removeChild(targetMed);
  }

</script>

</body>
</html>
