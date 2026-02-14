(function() {
  const iframe = document.createElement("iframe");
  iframe.src = "https://tools.samsonoyetola.com/assets/temp/expander.html";
  iframe.style.width = "100%";
  iframe.style.border = "none"; 

  document.getElementById("widget-holder").appendChild(iframe);
  
  
  // Listen for messages from the iframe
  window.addEventListener("message", function(event) {
    console.debug("Event Received", event);
    
      if (event.data && event.data.height) {
          iframe.style.height = event.data.height + "px";
      }
  });

  function adjustIframeHeight() {
    try {
      console.log('Iframe Origin:', iframe.contentWindow.location.origin);

      const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
      console.log("Iframe Info", {
        iframeDoc,
      });


      const height = iframeDoc.body.scrollHeight;
      console.log("Iframe Content Height", {
        height
      });


      iframe.style.height = height + "px";
    } catch (err) {
      console.error("Error adjusting iframe height:", err);
    }
  }



  iframe.onload = adjustIframeHeight;
  window.addEventListener("resize", adjustIframeHeight);
})();