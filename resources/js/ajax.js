var xhttp;
var csrf_token = document.querySelector('meta[name="csrf-token"]').content;

function ajax($callback, $url, $data, $post ){

    var data = [];
    data.push( '_token'+"="+csrf_token );
    if ($data){
        for (var d in $data) {
            if ($data.hasOwnProperty(d)) {
                data.push(encodeURIComponent(d) + "=" + encodeURIComponent($data[d]));
            }
        }
        data.join('&');
    }

    xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            return $callback(JSON.parse(this.responseText));
        }
    }
    if ($post){
        xhttp.open("POST", $url, true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send(data.join('&'));
    } else {
        if ($data){
            $url = $url + '?' + data.join('&');
        }
        xhttp.open("GET", $url, true);
        xhttp.send();
    }
}

export { ajax }
