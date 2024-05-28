<?php

include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
   $user_id = $_COOKIE['user_id'];
} else {
   setcookie('user_id', create_unique_id(), time() + 60 * 60 * 24 * 30, '/');
   header('location:index.php');
}

if (isset($_POST['check'])) {

   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);

   $total_rooms = 0;

   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   while ($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)) {
      $total_rooms += $fetch_bookings['rooms'];
   }

   // if the hotel has total 30 rooms 
   if ($total_rooms >= 30) {
      $warning_msg[] = 'rooms are not available';
   } else {
      $success_msg[] = 'rooms are available';
   }

}

if (isset($_POST['book'])) {

   $booking_id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $rooms = $_POST['rooms'];
   $rooms = filter_var($rooms, FILTER_SANITIZE_STRING);
   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);
   $check_out = $_POST['check_out'];
   $check_out = filter_var($check_out, FILTER_SANITIZE_STRING);
   $adults = $_POST['adults'];
   $adults = filter_var($adults, FILTER_SANITIZE_STRING);
   $childs = $_POST['childs'];
   $childs = filter_var($childs, FILTER_SANITIZE_STRING);

   $total_rooms = 0;

   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   while ($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)) {
      $total_rooms += $fetch_bookings['rooms'];
   }

   if ($total_rooms >= 30) {
      $warning_msg[] = 'rooms are not available';
   } else {

      $verify_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE user_id = ? AND name = ? AND email = ? AND number = ? AND rooms = ? AND check_in = ? AND check_out = ? AND adults = ? AND childs = ?");
      $verify_bookings->execute([$user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);

      if ($verify_bookings->rowCount() > 0) {
         $warning_msg[] = 'room booked alredy!';
      } else {
         $book_room = $conn->prepare("INSERT INTO `bookings`(booking_id, user_id, name, email, number, rooms, check_in, check_out, adults, childs) VALUES(?,?,?,?,?,?,?,?,?,?)");
         $book_room->execute([$booking_id, $user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);
         $success_msg[] = 'room booked successfully!';
      }

   }

}

if (isset($_POST['send'])) {

   $id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $message = $_POST['message'];
   $message = filter_var($message, FILTER_SANITIZE_STRING);

   $verify_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
   $verify_message->execute([$name, $email, $number, $message]);

   if ($verify_message->rowCount() > 0) {
      $warning_msg[] = 'message sent already!';
   } else {
      $insert_message = $conn->prepare("INSERT INTO `messages`(id, name, email, number, message) VALUES(?,?,?,?,?)");
      $insert_message->execute([$id, $name, $email, $number, $message]);
      $success_msg[] = 'message send successfully!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Hauptseite</title>

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php include 'components/user_header.php'; ?>

   <!-- home section starts  -->

   <section class="home" id="home">

      <div class="swiper home-slider">

         <div class="swiper-wrapper">

            <div class="box swiper-slide">
               <img src="images/home-img-1.jpg" alt="">
               <div class="flex">
                  <h3>Luxuriöse Zimmer</h3>
                  <a href="#availability" class="btn">Verfügbarkeit prüfen</a>
               </div>
            </div>

            <div class="box swiper-slide">
               <img src="images/home-img-2.jpg" alt="">
               <div class="flex">
                  <h3>Essen und Trinken</h3>
                  <a href="#reservation" class="btn">reservieren</a>
               </div>
            </div>

            <div class="box swiper-slide">
               <img src="images/home-img-3.jpg" alt="">
               <div class="flex">
                  <h3>Luxuriöse Säle</h3>
                  <a href="#contact" class="btn">kontaktiere uns</a>
               </div>
            </div>

         </div>

         <div class="swiper-button-next"></div>
         <div class="swiper-button-prev"></div>

      </div>

   </section>

   <!-- home section ends -->

   <!-- availability section starts  -->

   <section class="availability" id="availability">

      <form action="" method="post">
         <div class="flex">
            <div class="box">
               <p>Check-In <span>*</span></p>
               <input type="date" name="check_in" class="input" required>
            </div>
            <div class="box">
               <p>Check-Out <span>*</span></p>
               <input type="date" name="check_out" class="input" required>
            </div>
            <div class="box">
               <p>Erwachsene <span>*</span></p>
               <select name="adults" class="input" required>
                  <option value="1">1 Erwachsener</option>
                  <option value="2">2 Erwachsene</option>
                  <option value="3">3 Erwachsene</option>
                  <option value="4">4 Erwachsene</option>
                  <option value="5">5 Erwachsene</option>
                  <option value="6">6 Erwachsene</option>
               </select>
            </div>
            <div class="box">
               <p>Kinder <span>*</span></p>
               <select name="childs" class="input" required>
                  <option value="-">0 Kinder</option>
                  <option value="1">1 Kind</option>
                  <option value="2">2 Kinder</option>
                  <option value="3">3 Kinder</option>
                  <option value="4">4 Kinder</option>
                  <option value="5">5 Kinder</option>
                  <option value="6">6 Kinder</option>
               </select>
            </div>
            <div class="box">
               <p>Zimmer <span>*</span></p>
               <select name="rooms" class="input" required>
                  <option value="1">1 Zimmer</option>
                  <option value="2">2 Zimmer</option>
                  <option value="3">3 Zimmer</option>
                  <option value="4">4 Zimmer</option>
                  <option value="5">5 Zimmer</option>
                  <option value="6">6 Zimmer</option>
               </select>
            </div>
         </div>
         <input type="submit" value="check availability" name="check" class="btn">
      </form>

   </section>

   <!-- availability section ends -->

   <!-- about section starts  -->

   <section class="about" id="about">

      <div class="row">
         <div class="image">
            <img src="images/about-img-1.jpg" alt="">
         </div>
         <div class="content">
            <h3>Das beste Personal</h3>
            <p>Unser Team bietet Ihnen herzliche Gastfreundschaft und professionellen Service. Mit
               langjähriger Erfahrung und Mehrsprachigkeit sind wir stets für Ihre Bedürfnisse da. Genießen Sie einen
               unvergesslichen Aufenthalt mit unserem engagierten Personal.</p>
            <a href="#reservation" class="btn">reservieren</a>
         </div>
      </div>

      <div class="row revers">
         <div class="image">
            <img src="images/about-img-2.jpg" alt="">
         </div>
         <div class="content">
            <h3>Das beste Essen</h3>
            <p>Entdecken Sie kulinarische Genüsse. Unser Restaurant bietet eine vielfältige Auswahl an
               köstlichen Gerichten, die von erfahrenen Köchen mit frischen Zutaten zubereitet werden. Genießen Sie ein
               unvergessliches kulinarisches Erlebnis in entspannter Atmosphäre.</p>
            <a href="#contact" class="btn">kontaktiere uns</a>
         </div>
      </div>

      <div class="row">
         <div class="image">
            <img src="images/about-img-3.jpg" alt="">
         </div>
         <div class="content">
            <h3>Schwimmbad</h3>
            <p>Entspannen Sie sich und erfrischen Sie sich in unserem Schwimmbad. Tauchen Sie ein in
               klares Wasser und genießen Sie erholsame Momente unter der Sonne. Unser Schwimmbad bietet Ihnen die
               perfekte Gelegenheit, sich zu entspannen und den Stress des Alltags hinter sich zu lassen.</p>
            <a href="#availability" class="btn">Verfügbarkeit prüfen</a>
         </div>
      </div>

   </section>

   <!-- about section ends -->

   <!-- services section starts  -->

   <section class="services">

      <div class="box-container">

         <div class="box">
            <img src="images/icon-1.png" alt="">
            <h3>Essen und Trinken</h3>
            <p>Wir bieten Ihnen eine exzellente Auswahl an Speisen und Getränken.

               Restaurant: Geniessen Sie lokale und internationale Spezialitäten in unserem Restaurant.
               Bar: Entspannen Sie sich in unserer Bar mit einer breiten Palette an Getränken und Cocktails.
               Zimmerservice: Unser 24-Stunden-Zimmerservice ermöglicht Ihnen, Ihre Mahlzeiten in Ihrem Zimmer zu genießen.
               Frühstück inklusive: Beginnen Sie den Tag mit einem reichhaltigen Frühstück, das im Zimmerpreis inbegriffen ist.

               Wir verwenden nur die besten Zutaten, um Ihnen unvergessliche kulinarische Momente zu bieten.
            </p>
         </div>

         <div class="box">
            <img src="images/icon-2.png" alt="">
            <h3>Essen im Freien</h3>
            <p>Geniessen Sie Ihre Mahlzeiten unter freiem Himmel in unserem charmanten Aussenbereich.

               Gartenrestaurant: Köstliche Speisen umgeben von üppigem Grün und entspannter Atmosphäre.
               Poolbar: Erfrischende Getränke und leichte Snacks am Pool, betreut von unserem freundlichen Personal.
               Terrasse: Spektakuläre Ausblicke und kulinarische Köstlichkeiten zu jeder Mahlzeit.

               Erleben Sie die perfekte Verbindung von gutem Essen und Natur.</p>
         </div>

         <div class="box">
            <img src="images/icon-3.png" alt="">
            <h3>Strandblick</h3>
            <p>Geniessen Sie spektakuläre Ausblicke auf den Strand und das Meer während Ihrer Mahlzeiten.

               Strandrestaurant: Exzellente Speisen mit atemberaubender Aussicht auf den Ozean und romantische Sonnenuntergänge.
               Meeresbrise: Speisen Sie auf unserer Terrasse mit Blick auf den Horizont und den feinen Sandstrand.
               Privates Dinieren am Strand: Romantische, exklusive Gerichte und persönlicher Service direkt am Strand.

               Erleben Sie kulinarische Genüsse in perfekter Harmonie mit der Schönheit der Natur.</p>
         </div>

         <div class="box">
            <img src="images/icon-4.png" alt="">
            <h3>Dekorationen</h3>
            <p>Tauchen Sie ein in eine Welt voller Eleganz und Stil dank unserer sorgfältig ausgewählten Dekorationen.

               Elegantes Ambiente: Stilvolle Dekorationen in der Lobby und den Gästezimmern vereinen Komfort und Luxus.
               Kunstwerke: Bewundern Sie lokale Kunstwerke, die jedem Raum eine besondere Note verleihen.
               Blumenschmuck: Frische Blumenarrangements schaffen eine belebende und duftende Atmosphäre.
               Saisonale Dekorationen: Angepasst an die Jahreszeiten bieten unsere Dekorationen ein festliches Erlebnis.

               Erleben Sie die Kunst der Dekorationen und lassen Sie sich von unserem ansprechenden
               Ambiente verzaubern.</p>
         </div>

         <div class="box">
            <img src="images/icon-5.png" alt="">
            <h3>Schwimmbad</h3>
            <p>Entspannen Sie Körper und Geist in unserem erstklassigen Schwimmbad.

            Erfrischender Genuss: Tauchen Sie in unser kristallklares Schwimmbad ein und erfrischen Sie sich nach einem ereignisreichen Tag.
            Panoramablick: Genießen Sie spektakuläre Ausblicke auf die Umgebung beim Schwimmen.
            Sonnenliegen: Entspannen Sie auf bequemen Sonnenliegen rund um das Schwimmbad und genießen Sie die Sonne.
            Poolbar: Genießen Sie erfrischende Getränke und leichte Snacks an unserer Poolbar.

               Erleben Sie unvergessliche Momente der Entspannung und Erholung in unserem Schwimmbad.</p>
         </div>

         <div class="box">
            <img src="images/icon-6.png" alt="">
            <h3>Resort-Strand</h3>
            <p>Entdecken Sie unseren exklusiven Strandbereich, wo Entspannung und Abenteuer auf Sie
               warten.

               Privater Zugang: Genießen Sie einen abgeschiedenen Strandabschnitt, exklusiv für unsere Gäste.
               Wassersportaktivitäten: Erleben Sie Schnorcheln, Tauchen oder Kajakfahren direkt vor Ort, unterstützt von unserem erfahrenen Personal.
               Strandbar: Verwöhnen Sie sich mit erfrischenden Getränken und Snacks an unserer Strandbar.
               Sonnenuntergänge: Genießen Sie spektakuläre Sonnenuntergänge und die romantische Atmosphäre am Strand.

               Erleben Sie unvergessliche Momente und lassen Sie sich von der Schönheit der Natur inspirieren.
            </p>
         </div>

      </div>

   </section>

   <!-- services section ends -->

   <!-- reservation section starts  -->

   <section class="reservation" id="reservation">

      <form action="" method="post">
         <h3>reservieren</h3>
         <div class="flex">
            <div class="box">
               <p>Ihr Name <span>*</span></p>
               <input type="text" name="name" maxlength="50" required placeholder="enter your name" class="input">
            </div>
            <div class="box">
               <p>Ihre E-Mail <span>*</span></p>
               <input type="email" name="email" maxlength="50" required placeholder="enter your email" class="input">
            </div>
            <div class="box">
               <p>Ihre Nummer <span>*</span></p>
               <input type="number" name="number" maxlength="10" min="0" max="9999999999" required
                  placeholder="enter your number" class="input">
            </div>
            <div class="box">
               <p>Zimmer <span>*</span></p>
               <select name="rooms" class="input" required>
                  <option value="1" selected>1 Zimmer</option>
                  <option value="2">2 Zimmer</option>
                  <option value="3">3 Zimmer</option>
                  <option value="4">4 Zimmer</option>
                  <option value="5">5 Zimmer</option>
                  <option value="6">6 Zimmer</option>
               </select>
            </div>
            <div class="box">
               <p>Check-In <span>*</span></p>
               <input type="date" name="check_in" class="input" required>
            </div>
            <div class="box">
               <p>Check-Out <span>*</span></p>
               <input type="date" name="check_out" class="input" required>
            </div>
            <div class="box">
               <p>Erwachsene <span>*</span></p>
               <select name="adults" class="input" required>
                  <option value="1" selected>1 Erwachsener</option>
                  <option value="2">2 Erwachsene</option>
                  <option value="3">3 Erwachsene</option>
                  <option value="4">4 Erwachsene</option>
                  <option value="5">5 Erwachsene</option>
                  <option value="6">6 Erwachsene</option>
               </select>
            </div>
            <div class="box">
               <p>Kinder <span>*</span></p>
               <select name="childs" class="input" required>
                  <option value="0" selected>0 Kinder</option>
                  <option value="1">1 Kind</option>
                  <option value="2">2 Kinder</option>
                  <option value="3">3 Kinder</option>
                  <option value="4">4 Kinder</option>
                  <option value="5">5 Kinder</option>
                  <option value="6">6 Kinder</option>
               </select>
            </div>
         </div>
         <input type="submit" value="book now" name="book" class="btn">
      </form>

   </section>

   <!-- reservation section ends -->

   <!-- gallery section starts  -->

   <section class="gallery" id="gallery">

      <div class="swiper gallery-slider">
         <div class="swiper-wrapper">
            <img src="images/gallery-img-1.jpg" class="swiper-slide" alt="">
            <img src="images/gallery-img-2.webp" class="swiper-slide" alt="">
            <img src="images/gallery-img-3.webp" class="swiper-slide" alt="">
            <img src="images/gallery-img-4.webp" class="swiper-slide" alt="">
            <img src="images/gallery-img-5.webp" class="swiper-slide" alt="">
            <img src="images/gallery-img-6.webp" class="swiper-slide" alt="">
         </div>
         <div class="swiper-pagination"></div>
      </div>

   </section>

   <!-- gallery section ends -->

   <!-- contact section starts  -->

   <section class="contact" id="contact">

      <div class="row">

         <form action="" method="post">
            <h3>Schicken Sie uns eine Nachricht</h3>
            <input type="text" name="name" required maxlength="50" placeholder="enter your name" class="box">
            <input type="email" name="email" required maxlength="50" placeholder="enter your email" class="box">
            <input type="number" name="number" required maxlength="10" min="0" max="9999999999"
               placeholder="enter your number" class="box">
            <textarea name="message" class="box" required maxlength="1000" placeholder="enter your message" cols="30"
               rows="10"></textarea>
            <input type="submit" value="send message" name="send" class="btn">
         </form>

         <div class="faq">
            <h3 class="title">Häufig gestellte Fragen</h3>
            <div class="box active">
               <h3>Wie kündige ich?</h3>
               <p>Sie können Ihre Reservierung bei uns auf folgende Weise stornieren:

                  Online: Nutzen Sie die Funktion zur Online-Stornierung auf unserer Webseite.
                  Telefonisch: Kontaktieren Sie unser Reservierungsteam.
                  Per E-Mail: Senden Sie uns eine E-Mail mit Ihrer Buchungsnummer und dem Stornierungswunsch an
                  [E-Mail-Adresse].

                  Bitte beachten Sie unsere Stornierungsbedingungen.
               </p>
            </div>
            <div class="box">
               <h3>Was sind Zahlungsmethoden?</h3>
               <p>Wir akzeptieren folgende Zahlungsmethoden:

                  Kreditkarten: Visa, MasterCard, American Express und andere.
                  Debitkarten: Mit Visa- oder MasterCard-Logo.
                  Barzahlung: Je nach Verfügbarkeit möglich.
                  Banküberweisung: Für Gruppenbuchungen oder längere Aufenthalte.

                  Für spezielle Anfragen stehen wir Ihnen gerne zur Verfügung.</p>
            </div>
            <div class="box">
               <h3>Wie erhalte ich Gutscheincodes?</h3>
               <p>Es gibt verschiedene Möglichkeiten, um Gutscheincodes zu erhalten:

                  Newsletter: Abonnieren Sie Newsletter von Unternehmen oder Marken, um exklusive Angebote und
                  Gutscheincodes per E-Mail zu erhalten.
                  Soziale Medien: Folgen Sie Unternehmen auf Social-Media-Plattformen wie Facebook, Twitter oder
                  Instagram, um keine Sonderaktionen oder Gutscheincodes zu verpassen.
                  Werbeaktionen: Halten Sie Ausschau nach Werbeaktionen oder Sonderangeboten auf Webseiten oder in
                  Geschäften. Unternehmen bieten regelmäßig spezielle Rabatte und Gutscheincodes für bestimmte Zeiträume
                  oder Veranstaltungen an.</p>
            </div>
            <div class="box">
               <h3>Was sind die Altersanforderungen?</h3>
               <p> Check-In im Hotel: Gäste müssen mindestens 18 Jahre alt sein, um selbstständig ein Zimmer zu buchen
                  und einzuchecken. Personen unter 18 Jahren benötigen möglicherweise eine schriftliche Genehmigung
                  eines Elternteils oder Erziehungsberechtigten.
                  Veranstaltungen und Aktivitäten: Das Mindestalter für bestimmte Veranstaltungen, Aktivitäten oder
                  Einrichtungen wie Wellnessbereiche, Fitnessstudios oder Casinos kann variieren. Bitte beachten Sie die
                  spezifischen Altersbeschränkungen für jede Aktivität.
                  Alkoholkonsum: Das Mindestalter für den Konsum von Alkohol in öffentlichen Bereichen oder beim Kauf
                  von alkoholischen Getränken kann je nach Land und lokalen Gesetzen unterschiedlich sein. In der Regel
                  liegt das Mindestalter jedoch bei 18 oder 21 Jahren.</p>
            </div>
         </div>

      </div>

   </section>

   <!-- contact section ends -->

   <!-- reviews section starts  -->

   <section class="reviews" id="reviews">

      <div class="swiper reviews-slider">

         <div class="swiper-wrapper">
            <div class="swiper-slide box">
               <img src="images/pic-1.png" alt="">
               <h3>Jussif Abdel-Rahman</h3>
               <p>Dieses Hotel übertraf alle meine Erwartungen! Vom herzlichen Empfang bis hin zum atemberaubenden Blick
                  aus meinem Zimmer war jeder Moment ein Genuss. Das Personal war äußerst zuvorkommend und die
                  Annehmlichkeiten waren erstklassig. Ich kann es kaum erwarten, zurückzukehren!</p>
            </div>
            <div class="swiper-slide box">
               <img src="images/pic-2.png" alt="">
               <h3>Leonardo Kalbermatter</h3>
               <p>ch konnte mich in diesem Hotel vollkommen entspannen. Der Spa-Bereich war himmlisch und das Schwimmbad
                  bot einen herrlichen Blick auf die Umgebung. Das Essen war köstlich und das Personal stand immer
                  bereit, um sicherzustellen, dass mein Aufenthalt unvergesslich wurde. Absolut empfehlenswert für alle,
                  die eine Auszeit vom Alltag suchen!</p>
            </div>
            <div class="swiper-slide box">
               <img src="images/pic-3.png" alt="">
               <h3>Hans Franz</h3>
               <p>Unser Aufenthalt in diesem Hotel war einfach magisch! Der Strandabschnitt des Resorts war einfach
                  traumhaft und perfekt für lange Spaziergänge am Meer. Das Zimmer bot einen atemberaubenden Blick auf
                  den Sonnenuntergang, und das freundliche Personal sorgte dafür, dass wir uns wie zu Hause fühlten. Ich
                  kann es kaum erwarten, wieder hierher zurückzukehren!</p>
            </div>
            <div class="swiper-slide box">
               <img src="images/pic-4.png" alt="">
               <h3>Cristiano Ronaldo</h3>
               <p>Wir waren begeistert von der Vielfalt und Qualität des Essens in diesem Hotel. Vom reichhaltigen
                  Frühstücksbuffet bis hin zu den exquisiten Abendessen im hauseigenen Restaurant wurden unsere
                  Geschmacksknospen ständig verwöhnt. Ein absolutes Muss für Feinschmecker!</p>
            </div>
            <div class="swiper-slide box">
               <img src="images/pic-5.png" alt="">
               <h3>Thomas Schmidt</h3>
               <p>Unser Familienurlaub in diesem Hotel war ein voller Erfolg! Die Kinder liebten den Poolbereich und den
                  Spielplatz, während die Erwachsenen die entspannte Atmosphäre und die erstklassigen Einrichtungen
                  genossen. Das Personal war äußerst hilfsbereit und freundlich und sorgte dafür, dass wir uns rundum
                  wohl fühlten. Wir werden auf jeden Fall wiederkommen!</p>
            </div>
            <div class="swiper-slide box">
               <img src="images/pic-6.png" alt="">
               <h3>John Imboden</h3>
               <p>Dieses Hotel war der ideale Ausgangspunkt für unsere Abenteuer in der Umgebung. Wir konnten problemlos
                  die nahegelegenen Sehenswürdigkeiten erkunden und kehrten jeden Abend gerne in unser gemütliches
                  Zimmer zurück. Die Lage, der Service und die Atmosphäre machten unseren Urlaub unvergesslich. Sehr
                  empfehlenswert für Reisende, die gerne die Welt erkunden möchten!</p>
            </div>
         </div>

         <div class="swiper-pagination"></div>
      </div>

   </section>

   <!-- reviews section ends  -->





   <?php include 'components/footer.php'; ?>

   <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

   <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

   <!-- custom js file link  -->
   <script src="js/script.js"></script>

   <?php include 'components/message.php'; ?>

</body>

</html>