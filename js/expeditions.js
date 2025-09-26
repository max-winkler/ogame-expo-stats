const ship_names = {'transporterSmall': 'Kleiner Transporter',
		'transporterLarge': 'Großer Transporter',
		'colonyShip': 'Kolonieschiff',
		'recycler': 'Recycler',
		'espionageProbe': 'Spionagesonde',         
		'fighterLight': 'Leichter Jäger',
		'figherHeavy': 'Schwerer Jäger',
		'cruiser': 'Kreuzer',
		'battleship': 'Schlachtschiff',
		'interceptor': 'Schlachtkreuzer',
		'bomber': 'Bomber',
		'destroyer': 'Zerstörer',
		'deathstar': 'Todesstern',
		'reaper': 'Reaper',
		'explorer': 'Pathfinder'};

function get_ship_key(name)
{
    for(key in ship_names)
        if(name == ship_names[key])
	  return key;
    return 'Unkown';
}

function clear_expo_report()
{
    $('#expo_report').val("");
}

function parse_expo_report()
{
    var reports = $('#expo_report').val().split("Expeditionsergebnis");
    var results = [];
        
    for(var i in reports)
    {
        var report = reports[i].trim();

        if(report == '')
	  continue;

        report = report.replace('Mehr Details', '');
        var result = {};
        
        // Get timestamp of the report
        var date = report.match(/\d{2}([\/.-])\d{2}\1\d{4}/g);
        var time = report.match(/\d{2}(:)\d{2}\1\d{2}/g);
        
        result['timestamp'] = date+' '+time;        
        
        // Fleet was found
        if(report.includes("Folgende Schiffe schlossen sich der Flotte an:"))
        {
	  result['type'] = 'F';

	  var fleetString = report.split("Folgende Schiffe schlossen sich der Flotte an:")[1];
	  var fleetDetails = fleetString.split(/:|\n/);

	  fleetDetails = fleetDetails.map(function(e) {return e.trim();});
	  fleetDetails = fleetDetails.filter(function(e) {return e != '';});

	  for(var i=0; i<fleetDetails.length/2; ++i)
	  {
	      var type = fleetDetails[2*i];
	      var count = fleetDetails[2*i+1];
	      
	      type = get_ship_key(type);

	      result[type] = count;
	      console.log(type + " - " + count);
	  }	   
        }
        
        // Ressources or DM found
        var res_string = report.match(/Es wurde (.*) erbeutet/);
        if(res_string != null)
        {  
	  res_string = res_string[1];

	  result['type'] = 'R';
	  
	  if(res_string.includes('Metall'))
	      result['metall'] = res_string.split('Metall')[1].trim();
	  else if(res_string.includes('Kristall'))
	      result['crystal'] = res_string.split('Kristall')[1].trim();
	  else if(res_string.includes('Deuterium'))
	      result['deuterium'] = res_string.split('Deuterium')[1].trim();
	  else if(res_string.includes('Dunkle Materie'))
	  {
	      result['darkmatter'] = res_string.split('Dunkle Materie')[1].trim();
	      result['type'] = 'D';
	  }

        }

        // Nothing happened at expo
        if(result['type'] === undefined)
        {
	  result['type'] = 'N';
        }
        
        results.push(result);        
    }
    console.log(results);
    return results;
}

function read_expo_report()
{
    var reports = parse_expo_report();    
    console.log(JSON.stringify(reports));
    request = '<form method="post" action=""><input type="hidden" name="action" value="confirm"><input type="hidden" name="user" value="Moa Katanga"><input type="hidden" name="reports"></form>';
    
    var form = $(request).appendTo('body');
    $('input[name ="reports"]').attr('value', btoa(JSON.stringify(reports)));
    form.submit();
}


function add_new_user()
{
    var name = $('#user_input').val();

    if(name === '')
        return;

    
    alert('this will add the user '+name+' to the database');
}
