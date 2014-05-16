var url=location.protocol+'//'+location.host;
/*importScripts(url+'/');*/
onmessage = function (event) {
	var d,time,raw,datetime;
	setInterval(
		function(event){
			d=new Date();
			postMessage({
				datetime:d.toISOString(),
				norm:d.toLocaleTimeString(),
				date:d
			});
		}
	,1000);
};
