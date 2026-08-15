-- Conteúdo da página Política de Privacidade — pt-BR (texto real do site) + EN/ES/IT.

INSERT INTO ui_strings (string_key, group_name, description, sort_order) VALUES
('privacy.page_title', 'privacy', 'Política de Privacidade — título da página (cabeçalho)', 1),
('privacy.content', 'privacy', 'Política de Privacidade — corpo completo do texto (HTML)', 2)
ON DUPLICATE KEY UPDATE description = VALUES(description);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'pt-BR', v FROM ui_strings JOIN (
  SELECT 'privacy.page_title' k, 'Política de privacidade' v UNION ALL
  SELECT 'privacy.content', '<h3 class="title large-text side-lines bottom-line">Quem somos</h3>
<p>O endereço do nosso site é: https://www.esterni.ind.br/.</p>
<div style="padding-bottom: 2rem;"></div>
<h3 class="title large-text side-lines bottom-line">Quais dados pessoais coletamos e porque</h3>
<h4 class="title">Inscrições</h4>
<p>Inscrições em listas de e-mail ou newsletter. Quando você faz a sua inscrição pelo website da Esterni para recebimento de nossos e-mails, nós solicitamos que você forneça o endereço de e-mail e nome completo. Tais informações são usadas para identificar seu cadastro em nosso banco de dados, e para enviar os e-mails para você. Nesse caso, usamos do seu consentimento para que possamos realizar esse tratamento de dados em conformidade com a Lei Geral de Proteção de Dados (art. 7º, inciso I e art. 8º).</p>
<div style="padding-bottom: 2rem;"></div>
<h4 class="title">Formulários de contato</h4>
<p>Quando os visitantes enviam mensagens através do site, coletamos os dados mostrados no formulário, além do endereço de IP e de dados do navegador do visitante, para auxiliar na detecção de spam e para devido acompanhamento de seu contato pela nossa equipe.</p>
<div style="padding-bottom: 2rem;"></div>
<h4 class="title">Cookies</h4>
<p>Ao deixar um comentário no site, você poderá optar por salvar seu nome, e-mail e site nos cookies. Isso visa seu conforto, assim você não precisará preencher seus dados novamente quando fizer outro comentário. Estes cookies duram um ano.</p>
<p>Ao preencher o formulário de contato no site, você poderá optar por salvar seu nome e e-mail nos cookies. Isso visa seu conforto, assim você não precisará preencher seus dados novamente quando enviar outra mensagem. Estes cookies duram um ano.</p>
<p>Se você tem uma conta e acessa este site, um cookie temporário será criado para determinar se seu navegador aceita cookies. Ele não contém nenhum dado pessoal e será descartado quando você fechar seu navegador.</p>
<p>Quando você acessa sua conta no site, também criamos vários cookies para salvar os dados da sua conta e suas escolhas de exibição de tela. Cookies de login são mantidos por dois dias e cookies de opções de tela por um ano. Se você selecionar "Lembrar-me", seu acesso será mantido por duas semanas. Se você se desconectar da sua conta, os cookies de login serão removidos.</p>
<div style="padding-bottom: 2rem;"></div>
<h4 class="title">Mídia incorporada de outros sites</h4>
<p>Artigos neste site podem incluir conteúdo incorporado como, por exemplo, vídeos, imagens, artigos, etc. Conteúdos incorporados de outros sites se comportam exatamente da mesma forma como se o visitante estivesse visitando o outro site.</p>
<p>Estes sites podem coletar dados sobre você, usar cookies, incorporar rastreamento adicional de terceiros e monitorar sua interação com este conteúdo incorporado, incluindo sua interação com o conteúdo incorporado se você tem uma conta e está conectado com o site.</p>
<div style="padding-bottom: 2rem;"></div>
<h3 class="title large-text side-lines bottom-line">Com quem partilhamos seus dados</h3>
<p>Os seus dados referentes à contatos poderão ser compartilhados com nossa equipe, a fim de prosseguir com o atendimento ou retorno de contato, e envio de novidades, se solicitado por você.</p>
<div style="padding-bottom: 2rem;"></div>
<h3 class="title large-text side-lines bottom-line">Por quanto tempo mantemos os seus dados</h3>
<p>Se você enviar um formulário, os dados do formulário serão conservados por um prazo de 12 meses.</p>
<p>Para usuários que se registram no nosso site, também guardamos as informações pessoais que fornecem no seu perfil de usuário. Para alteração de dados relacionados à conta / login, por favor, entre em contato conosco através do e-mail comercial@esterni.ind.br. Os administradores do site também podem ver e editar estas informações.</p>
<div style="padding-bottom: 2rem;"></div>
<h3 class="title large-text side-lines bottom-line">Quais os seus direitos sobre seus dados</h3>
<p>Se você tiver uma conta neste site, pode solicitar um arquivo exportado dos dados pessoais que mantemos sobre você, inclusive quaisquer dados que nos tenha fornecido. Também pode solicitar que removamos qualquer dado pessoal que mantemos sobre você. Isto não inclui nenhum dado que somos obrigados a manter para propósitos administrativos, legais ou de segurança.</p>
<div style="padding-bottom: 2rem;"></div>
<h3 class="title large-text side-lines bottom-line">Para onde enviamos seus dados</h3>
<p>Formulários de visitantes podem ser marcados por um serviço automático de detecção de spam.</p>
<div style="padding-bottom: 2rem;"></div>
<h3 class="title large-text side-lines bottom-line">Informações de contato</h3>
<p>Para exercer os direitos dos seus titulares de dados, entre em contato com comercial@esterni.ind.br.<br>
TECHNOMAST INDÚSTRIA METALÚRGICA LTDA (07.972.180/0001-12).<br>
Rodovia PR 423, Km 24.3, S/N, Barracão C, Jardim das Acácias, Campo Largo-PR | CEP: 83603-000 | Telefone: (41) 3195-4348</p>'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'en', v FROM ui_strings JOIN (
  SELECT 'privacy.page_title' k, 'Privacy Policy' v UNION ALL
  SELECT 'privacy.content', '<h3 class="title large-text side-lines bottom-line">Who we are</h3>
<p>Our website address is: https://www.esterni.ind.br/.</p>
<div style="padding-bottom: 2rem;"></div>
<h3 class="title large-text side-lines bottom-line">What personal data we collect and why</h3>
<h4 class="title">Newsletter sign-ups</h4>
<p>When you sign up through the Esterni website to receive our emails, we ask you to provide your email address and full name. This information is used to identify your record in our database and to send emails to you. In this case, we rely on your consent to carry out this data processing in accordance with the Brazilian General Data Protection Law (LGPD, art. 7, I and art. 8).</p>
<div style="padding-bottom: 2rem;"></div>
<h4 class="title">Contact forms</h4>
<p>When visitors send messages through the site, we collect the data shown in the form, as well as the visitor''s IP address and browser data, to help with spam detection and to properly follow up on your contact with our team.</p>
<div style="padding-bottom: 2rem;"></div>
<h4 class="title">Cookies</h4>
<p>If you leave a comment on the site, you may opt in to save your name, email, and website in cookies. This is for your convenience so you do not have to fill in your details again when you leave another comment. These cookies last for one year.</p>
<p>If you fill in the contact form on the site, you may opt in to save your name and email in cookies. This is for your convenience so you do not have to fill in your details again when sending another message. These cookies last for one year.</p>
<p>If you have an account and log in to this site, a temporary cookie will be created to determine whether your browser accepts cookies. It contains no personal data and is discarded when you close your browser.</p>
<p>When you log into your account, we also set up several cookies to save your account information and your screen display choices. Login cookies last for two days, and screen options cookies last for a year. If you select "Remember Me", your login will persist for two weeks. If you log out of your account, the login cookies will be removed.</p>
<div style="padding-bottom: 2rem;"></div>
<h4 class="title">Embedded content from other websites</h4>
<p>Articles on this site may include embedded content (e.g. videos, images, articles, etc.). Embedded content from other websites behaves in the exact same way as if the visitor has visited the other website.</p>
<p>These websites may collect data about you, use cookies, embed additional third-party tracking, and monitor your interaction with that embedded content, including tracking your interaction with the embedded content if you have an account and are logged in to that website.</p>
<div style="padding-bottom: 2rem;"></div>
<h3 class="title large-text side-lines bottom-line">Who we share your data with</h3>
<p>Your contact-related data may be shared with our team in order to follow up on service or return your contact, and to send you news, if requested by you.</p>
<div style="padding-bottom: 2rem;"></div>
<h3 class="title large-text side-lines bottom-line">How long we retain your data</h3>
<p>If you submit a form, its data will be retained for 12 months.</p>
<p>For users who register on our site, we also store the personal information they provide in their user profile. To change or delete data related to your account/login, please contact us via comercial@esterni.ind.br. Site administrators can also see and edit that information.</p>
<div style="padding-bottom: 2rem;"></div>
<h3 class="title large-text side-lines bottom-line">What rights you have over your data</h3>
<p>If you have an account on this site, you can request an exported file of the personal data we hold about you, including any data you have provided to us. You can also request that we erase any personal data we hold about you. This does not include any data we are obliged to keep for administrative, legal, or security purposes.</p>
<div style="padding-bottom: 2rem;"></div>
<h3 class="title large-text side-lines bottom-line">Where we send your data</h3>
<p>Visitor form submissions may be checked through an automated spam detection service.</p>
<div style="padding-bottom: 2rem;"></div>
<h3 class="title large-text side-lines bottom-line">Contact information</h3>
<p>To exercise your data subject rights, please contact comercial@esterni.ind.br.<br>
TECHNOMAST INDÚSTRIA METALÚRGICA LTDA (Brazilian tax ID 07.972.180/0001-12).<br>
Rodovia PR 423, Km 24.3, S/N, Barracão C, Jardim das Acácias, Campo Largo-PR, Brazil | ZIP: 83603-000 | Phone: +55 (41) 3195-4348</p>'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'es', v FROM ui_strings JOIN (
  SELECT 'privacy.page_title' k, 'Política de privacidad' v UNION ALL
  SELECT 'privacy.content', '<h3 class="title large-text side-lines bottom-line">Quiénes somos</h3>
<p>La dirección de nuestro sitio web es: https://www.esterni.ind.br/.</p>
<div style="padding-bottom: 2rem;"></div>
<h3 class="title large-text side-lines bottom-line">Qué datos personales recopilamos y por qué</h3>
<h4 class="title">Suscripciones</h4>
<p>Suscripciones a listas de correo o boletín. Cuando te suscribes a través del sitio web de Esterni para recibir nuestros correos, te solicitamos tu dirección de correo electrónico y nombre completo. Esta información se utiliza para identificar tu registro en nuestra base de datos y para enviarte los correos. En este caso, utilizamos tu consentimiento para realizar este tratamiento de datos de conformidad con la Ley General de Protección de Datos de Brasil (LGPD, art. 7, I y art. 8).</p>
<div style="padding-bottom: 2rem;"></div>
<h4 class="title">Formularios de contacto</h4>
<p>Cuando los visitantes envían mensajes a través del sitio, recopilamos los datos mostrados en el formulario, además de la dirección IP y los datos del navegador del visitante, para ayudar en la detección de spam y para el debido seguimiento de tu contacto por parte de nuestro equipo.</p>
<div style="padding-bottom: 2rem;"></div>
<h4 class="title">Cookies</h4>
<p>Si dejas un comentario en el sitio, puedes optar por guardar tu nombre, correo electrónico y sitio web en cookies. Esto es para tu comodidad, para que no tengas que volver a completar tus datos cuando dejes otro comentario. Estas cookies duran un año.</p>
<p>Si completas el formulario de contacto del sitio, puedes optar por guardar tu nombre y correo electrónico en cookies. Esto es para tu comodidad, para que no tengas que volver a completar tus datos al enviar otro mensaje. Estas cookies duran un año.</p>
<p>Si tienes una cuenta y accedes a este sitio, se creará una cookie temporal para determinar si tu navegador acepta cookies. No contiene ningún dato personal y se descarta cuando cierras el navegador.</p>
<p>Al acceder a tu cuenta, también creamos varias cookies para guardar los datos de tu cuenta y tus preferencias de visualización. Las cookies de inicio de sesión se mantienen durante dos días y las de opciones de pantalla durante un año. Si seleccionas "Recuérdame", tu acceso se mantendrá durante dos semanas. Si cierras sesión, las cookies de inicio de sesión se eliminarán.</p>
<div style="padding-bottom: 2rem;"></div>
<h4 class="title">Contenido incrustado de otros sitios web</h4>
<p>Los artículos de este sitio pueden incluir contenido incrustado (por ejemplo, vídeos, imágenes, artículos, etc.). El contenido incrustado de otros sitios web se comporta exactamente igual que si el visitante hubiera visitado el otro sitio web.</p>
<p>Estos sitios web pueden recopilar datos sobre ti, usar cookies, incrustar rastreo adicional de terceros y monitorear tu interacción con ese contenido incrustado, incluido el seguimiento de tu interacción si tienes una cuenta y has iniciado sesión en ese sitio web.</p>
<div style="padding-bottom: 2rem;"></div>
<h3 class="title large-text side-lines bottom-line">Con quién compartimos tus datos</h3>
<p>Tus datos de contacto podrán ser compartidos con nuestro equipo, con el fin de dar seguimiento a la atención o devolver tu contacto, y para enviarte novedades, si así lo solicitas.</p>
<div style="padding-bottom: 2rem;"></div>
<h3 class="title large-text side-lines bottom-line">Durante cuánto tiempo conservamos tus datos</h3>
<p>Si envías un formulario, sus datos se conservarán durante un plazo de 12 meses.</p>
<p>Para los usuarios que se registran en nuestro sitio, también almacenamos la información personal que proporcionan en su perfil de usuario. Para modificar los datos relacionados con la cuenta/inicio de sesión, ponte en contacto con nosotros a través de comercial@esterni.ind.br. Los administradores del sitio también pueden ver y editar esta información.</p>
<div style="padding-bottom: 2rem;"></div>
<h3 class="title large-text side-lines bottom-line">Qué derechos tienes sobre tus datos</h3>
<p>Si tienes una cuenta en este sitio, puedes solicitar un archivo exportado de los datos personales que mantenemos sobre ti, incluidos los que nos hayas proporcionado. También puedes solicitar que eliminemos cualquier dato personal que mantengamos sobre ti. Esto no incluye ningún dato que estemos obligados a conservar por motivos administrativos, legales o de seguridad.</p>
<div style="padding-bottom: 2rem;"></div>
<h3 class="title large-text side-lines bottom-line">A dónde enviamos tus datos</h3>
<p>Los envíos de formularios de visitantes pueden ser revisados por un servicio automático de detección de spam.</p>
<div style="padding-bottom: 2rem;"></div>
<h3 class="title large-text side-lines bottom-line">Información de contacto</h3>
<p>Para ejercer tus derechos como titular de los datos, ponte en contacto con comercial@esterni.ind.br.<br>
TECHNOMAST INDÚSTRIA METALÚRGICA LTDA (CNPJ 07.972.180/0001-12).<br>
Rodovia PR 423, Km 24.3, S/N, Barracão C, Jardim das Acácias, Campo Largo-PR, Brasil | CP: 83603-000 | Teléfono: (+55 41) 3195-4348</p>'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'it', v FROM ui_strings JOIN (
  SELECT 'privacy.page_title' k, 'Informativa sulla privacy' v UNION ALL
  SELECT 'privacy.content', '<h3 class="title large-text side-lines bottom-line">Chi siamo</h3>
<p>L''indirizzo del nostro sito web è: https://www.esterni.ind.br/.</p>
<div style="padding-bottom: 2rem;"></div>
<h3 class="title large-text side-lines bottom-line">Quali dati personali raccogliamo e perché</h3>
<h4 class="title">Iscrizioni</h4>
<p>Iscrizioni a liste e-mail o newsletter. Quando ti iscrivi tramite il sito web di Esterni per ricevere le nostre e-mail, ti chiediamo di fornire il tuo indirizzo e-mail e nome completo. Queste informazioni vengono utilizzate per identificare la tua registrazione nel nostro database e per inviarti le e-mail. In questo caso, ci basiamo sul tuo consenso per effettuare questo trattamento dei dati in conformità con la Legge Generale sulla Protezione dei Dati brasiliana (LGPD, art. 7, I e art. 8).</p>
<div style="padding-bottom: 2rem;"></div>
<h4 class="title">Moduli di contatto</h4>
<p>Quando i visitatori inviano messaggi tramite il sito, raccogliamo i dati mostrati nel modulo, oltre all''indirizzo IP e ai dati del browser del visitatore, per aiutare nel rilevamento dello spam e per il corretto seguito del tuo contatto da parte del nostro team.</p>
<div style="padding-bottom: 2rem;"></div>
<h4 class="title">Cookie</h4>
<p>Se lasci un commento sul sito, puoi scegliere di salvare il tuo nome, e-mail e sito web nei cookie. Questo è per tua comodità, in modo da non dover reinserire i tuoi dati quando lasci un altro commento. Questi cookie durano un anno.</p>
<p>Se compili il modulo di contatto del sito, puoi scegliere di salvare il tuo nome ed e-mail nei cookie. Questo è per tua comodità, in modo da non dover reinserire i tuoi dati quando invii un altro messaggio. Questi cookie durano un anno.</p>
<p>Se hai un account e accedi a questo sito, verrà creato un cookie temporaneo per determinare se il tuo browser accetta i cookie. Non contiene dati personali e viene eliminato alla chiusura del browser.</p>
<p>Quando accedi al tuo account, creiamo anche diversi cookie per salvare i dati del tuo account e le tue preferenze di visualizzazione dello schermo. I cookie di accesso durano due giorni, mentre quelli relativi alle opzioni dello schermo durano un anno. Se selezioni "Ricordami", il tuo accesso verrà mantenuto per due settimane. Se effettui il logout, i cookie di accesso verranno rimossi.</p>
<div style="padding-bottom: 2rem;"></div>
<h4 class="title">Contenuti incorporati da altri siti web</h4>
<p>Gli articoli di questo sito possono includere contenuti incorporati (ad es. video, immagini, articoli, ecc.). I contenuti incorporati da altri siti web si comportano esattamente come se il visitatore avesse visitato l''altro sito web.</p>
<p>Questi siti web potrebbero raccogliere dati su di te, utilizzare cookie, incorporare ulteriori strumenti di tracciamento di terze parti e monitorare la tua interazione con tali contenuti incorporati, incluso il tracciamento della tua interazione se hai un account e hai effettuato l''accesso a quel sito web.</p>
<div style="padding-bottom: 2rem;"></div>
<h3 class="title large-text side-lines bottom-line">Con chi condividiamo i tuoi dati</h3>
<p>I tuoi dati di contatto potranno essere condivisi con il nostro team, al fine di dare seguito all''assistenza o al tuo contatto, e per inviarti novità, se richiesto.</p>
<div style="padding-bottom: 2rem;"></div>
<h3 class="title large-text side-lines bottom-line">Per quanto tempo conserviamo i tuoi dati</h3>
<p>Se invii un modulo, i suoi dati verranno conservati per un periodo di 12 mesi.</p>
<p>Per gli utenti che si registrano sul nostro sito, conserviamo anche le informazioni personali fornite nel loro profilo utente. Per modificare i dati relativi all''account/accesso, contattaci tramite comercial@esterni.ind.br. Gli amministratori del sito possono anche visualizzare e modificare queste informazioni.</p>
<div style="padding-bottom: 2rem;"></div>
<h3 class="title large-text side-lines bottom-line">Quali diritti hai sui tuoi dati</h3>
<p>Se hai un account su questo sito, puoi richiedere un file esportato dei dati personali che conserviamo su di te, compresi quelli che ci hai fornito. Puoi anche richiedere la cancellazione di qualsiasi dato personale che conserviamo su di te. Questo non include i dati che siamo obbligati a conservare per scopi amministrativi, legali o di sicurezza.</p>
<div style="padding-bottom: 2rem;"></div>
<h3 class="title large-text side-lines bottom-line">Dove inviamo i tuoi dati</h3>
<p>Gli invii dei moduli dei visitatori possono essere controllati tramite un servizio automatico di rilevamento dello spam.</p>
<div style="padding-bottom: 2rem;"></div>
<h3 class="title large-text side-lines bottom-line">Informazioni di contatto</h3>
<p>Per esercitare i tuoi diritti come interessato, contatta comercial@esterni.ind.br.<br>
TECHNOMAST INDÚSTRIA METALÚRGICA LTDA (P.IVA 07.972.180/0001-12).<br>
Rodovia PR 423, Km 24.3, S/N, Barracão C, Jardim das Acácias, Campo Largo-PR, Brasile | CAP: 83603-000 | Telefono: (+55 41) 3195-4348</p>'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);
