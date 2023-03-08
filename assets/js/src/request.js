if(document.querySelector('.copy-to-clipboard')){
  // Get the element with the 'copy-to-clipboard' class
  const copyBtn = document.querySelector('.copy-to-clipboard');

  // Get the value of the 'data-to-copy' attribute
  const copyText = copyBtn.getAttribute('data-to-copy');

  // Add a click event listener to the button
  copyBtn.addEventListener('click', function() {
    // Create a new textarea element to hold the copied text
    const textarea = document.createElement('textarea');
    textarea.value = copyText;
    document.body.appendChild(textarea);

    // Select the text in the textarea and copy it to the clipboard
    textarea.select();
    document.execCommand('copy');

    // Remove the textarea element from the DOM
    document.body.removeChild(textarea);

    // Show a success message to the user
  //   alert('Copied to clipboard: ' + copyText);
  });
}

if(document.querySelector('.generate-code')){
  document.querySelector('.generate-code').addEventListener('click',function(){
    const tag=document.querySelector('.generate-code');
    if(tag.hasAttribute('data-code')){
      window.location.href="/codeGenerated?codeATM="+tag.getAttribute('data-code')
    }else{
      if(document.querySelector('.error')){
    document.querySelector('.error').style.display='block';
      }
    }
  })
}