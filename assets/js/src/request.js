// function copyToClipboard(text) {
//         // create a temporary input element to hold the IBAN text
//         var tempInput = document.createElement("input");
//         tempInput.style = "position: absolute; left: -1000px; top: -1000px";
//         tempInput.value = text;
//         document.body.appendChild(tempInput);

//         // select the text inside the temporary input element
//         tempInput.select();
//         tempInput.setSelectionRange(0, 99999); /*For mobile devices*/

//         // copy the selected text to the clipboard
//         document.execCommand("copy");

//         // remove the temporary input element
//         document.body.removeChild(tempInput);

//         // show the modal window
//         var modal = new bootstrap.Modal(document.getElementById('staticBackdrop'));
//         modal.show();
//     }

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
