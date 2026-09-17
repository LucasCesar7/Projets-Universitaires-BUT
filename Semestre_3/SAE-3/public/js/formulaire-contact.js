function sendEmail(event) {
  event.preventDefault();

  const nom = document.getElementById("nom").value.trim();
  const email = document.getElementById("email").value.trim();
  const sujet = document.getElementById("sujet").value.trim();
  const message = document.getElementById("message").value.trim();

  const subject = "Demande de contact";
  const body = encodeURIComponent(
    `Bonjour,\n\n` +
    `👤 Nom : ${nom}\n` +
    `✉️ Email : ${email}\n` +
    `📄 Objet : ${sujet}\n` +
    `📝 Message :\n${message}\n\n` +
    `Cordialement, ${nom}`
  );

  window.location.href = `mailto:contact.cinevote@gmail.com?subject=${encodeURIComponent(subject)}&body=${body}`;

  const confirmation = document.getElementById("confirmation-message");
  confirmation.style.display = "block";
  confirmation.classList.add("show");


  setTimeout(() => {
    document.getElementById("nom").value = "";
    document.getElementById("email").value = "";
    document.getElementById("sujet").value = "";
    document.getElementById("message").value = "";
  }, 500);
}
