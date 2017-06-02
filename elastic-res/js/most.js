

var http = new XMLHttpRequest();
var url = "http://localhost:4043/elastic/elasticsearch/campaign/create";
var params = "name=felix&created_at=25-01-10&scheduled_time=8:00&file_path=c://file_path&start_date=25-10-09&end_date=25-11-10&updated_at=25-11-10";
http.open("POST", url, true);

//Send the proper header information along with the request
http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

http.onreadystatechange = function() {//Call a function when the state changes.
    if(http.readyState == 4 && http.status == 200) {
        alert(http.responseText);
    }
};
http.send(params);