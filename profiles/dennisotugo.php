<?php

if (!defined('DB_USER')) {
	require "../../config.php";

}

try {
	$conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE, DB_USER, DB_PASSWORD);
}

catch(PDOException $pe) {
	die("Could not connect to the database " . DB_DATABASE . ": " . $pe->getMessage());
}

$date_time = new DateTime('now', new DateTimezone('Africa/Lagos'));
global $conn;

if (isset($_POST['payload'])) {
	require "../answers.php";

	$question = trim($_POST['payload']);
	function isTraining($question)
	{
		if (strpos($question, 'train:') !== false) {
			return true;
		}

		return false;
	}

	function getAnswer()
	{
		global $question;
		global $conn;
		$sql = 'SELECT * FROM chatbot WHERE question LIKE "' . $question . '"';
		$answer_data_query = $conn->query($sql);
		$answer_data_query->setFetchMode(PDO::FETCH_ASSOC);
		$answer_data_result = $answer_data_query->fetchAll();
		$answer_data_index = 0;
		if (count($answer_data_result) > 0) {
			$answer_data_index = rand(0, count($answer_data_result) - 1);
		}

		if ($answer_data_result[$answer_data_index]["answer"] == "") {
			return 'train: question # answer # password';
		}

		if (containsVariables($answer_data_result[$answer_data_index]['answer']) || containsFunctions($answer_data_result[$answer_data_index]['answer'])) {
			$answer = resolveAnswer($answer_data_result[$answer_data_index]['answer']);
			return $answer;
		}
		else {
			return $answer_data_result[$answer_data_index]['answer'];
		}
	}

	function resolveQuestionFromTraining($question)
	{
		$start = 7;
		$end = strlen($question) - strpos($question, " # ");
		$new_question = substr($question, $start, -$end);
		return $new_question;
	}

	function resolveAnswerFromTraining($question)
	{
		$start = strpos($question, " # ") + 3;
		$answer = substr($question, $start);
		return $answer;
	}

		if (isTraining($question)) {
			$answer = resolveAnswerFromTraining($question);
			$question = strtolower(resolveQuestionFromTraining($question));
			$question_data = array(
				':question' => $question,
				':answer' => $answer
			);
			$sql = 'SELECT * FROM chatbot WHERE question = "' . $question . '"';
			$question_data_query = $conn->query($sql);
			$question_data_query->setFetchMode(PDO::FETCH_ASSOC);
			$question_data_result = $question_data_query->fetch();
			$sql = 'INSERT INTO chatbot ( question, answer )
      	    VALUES ( :question, :answer );';
			$q = $conn->prepare($sql);
			$q->execute($question_data);
			echo "Now I understand. No wahala, now try me again";
			return;
		}

	function containsVariables($answer)
	{
		if (strpos($answer, "{{") !== false && strpos($answer, "}}") !== false) {
			return true;
		}

		return false;
	}

	function containsFunctions($answer)
	{
		if (strpos($answer, "((") !== false && strpos($answer, "))") !== false) {
			return true;
		}

		return false;
	}

	function resolveAnswer($answer)
	{
		if (strpos($answer, "((") == "" && strpos($answer, "((") !== 0) {
			return $answer;
		}
		else {
			$start = strpos($answer, "((") + 2;
			$end = strlen($answer) - strpos($answer, "))");
			$function_found = substr($answer, $start, -$end);
			$replacable_text = substr($answer, $start, -$end);
			$new_answer = str_replace($replacable_text, $function_found() , $answer);
			$new_answer = str_replace("((", "", $new_answer);
			$new_answer = str_replace("))", "", $new_answer);
			return resolveAnswer($new_answer);
		}
	}

	$answer = getAnswer();
	echo $answer;
	exit();
}
else {
?>

<div class="profile">
						<h1>Dennis Otugo</h1>
						<p>Human Being &nbsp;&bull;&nbsp; Cyborg &nbsp;&bull;&nbsp; Never asked for this</p>

					</div>
  <div class="bot-body">
    <div class="messages-body">
      <div>
        <div class="message bot">
          <span class="content">Look alive</span>
        </div>
      </div>
	<div>
        <div class="message bot">
          <span class="content">What do you have in mind, Let's talk :) </span>
        </div>
      </div>
    </div>
    <div class="send-message-body">
      <input class="message-box" placeholder="Enter your words here..."/>
    </div>
  </div>

<style>
.profile {height: 100%;text-align: center;position: fixed;position: fixed;position: fixed;width: 50%;right: 0;background-color: #007bff}footer {display: none;padding: 0px !important}h1, h2, h3, h4, h5, h6 {color: white;text-align: center;bottom: 50%;left: 65%;position: fixed;font-family: Lato,'Helvetica Neue',Helvetica,Arial,sans-serif;font-weight: 700}p {position: fixed;bottom: 40%;left: 58%;line-height: 1.5;margin: 30px 0}.bot-body {max-width: 100% !important;position: fixed;margin: 32px auto;position: fixed;width: 100%;left: 0;bottom: 0px;height: 80%}.messages-body {overflow-y: scroll;height: 100%;background-color: #FFFFFF;color: #3A3A5E;padding: 10px;overflow: auto;width: 50%;padding-bottom: 50px;border-top-left-radius: 5px;border-top-right-radius: 5px}.messages-body > div {background-color: #FFFFFF;color: #3A3A5E;padding: 10px;overflow: auto;width: 100%;padding-bottom: 50px}.message {float: left;font-size: 16px;background-color: #007bff63;padding: 10px;display: inline-block;border-radius: 3px;position: relative;margin: 5px}.message: before {position: absolute;top: 0;content: '';width: 0;height: 0;border-style: solid}.message.bot: before {border-color: transparent #9cccff transparent transparent;border-width: 0 10px 10px 0;left: -9px}.color-change {border-radius: 5px;font-size: 20px;padding: 14px 80px;cursor: pointer;color: #fff;background-color: #00A6FF;font-size: 1.5rem;font-family: 'Roboto';font-weight: 100;border: 1px solid #fff;box-shadow: 2px 2px 5px #AFE9FF;transition-duration: 0.5s;-webkit-transition-duration: 0.5s;-moz-transition-duration: 0.5s}.color-change: hover {color: #006398;border: 1px solid #006398;box-shadow: 2px 2px 20px #AFE9FF}.message.you: before {border-width: 10px 10px 0 0;right: -9px;border-color: #edf3fd transparent transparent transparent}.message.you {float: right}.content {display: block;color: #000000}.send-message-body {border-right: solid black 3px;position: fixed;width: 50%;left: 0;bottom: 0px;box-sizing: border-box;box-shadow: 1px 1px 9px 0px rgba(1, 1, 1, 1)}.message-box {width: -webkit-fill-available;border: none;padding: 2px 4px;font-size: 18px}body {overflow: hidden;height: 100%;background: #FFFFFF !important}.container {max-width: 100% !important}.fixed-top {position: fixed !important;}
</style>
<script>
  window.onload = function () {
          $(document).keypress(function (e) {
                  if (e.which == 13) {
                          getResponse(getQuestion());
                  }
          });
  }

  function isUrl(string) {
          var expression =
                  /[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/gi;
          var regex = new RegExp(expression);
          var t = string;
          if (t.match(regex)) {
                  return true;
          } else {
                  return false;
          }
  }

  function stripHTML(message) {
          var re = /<\S[^><]*>/g
          return message.replace(re, "");
  }

  function getResponse(question) {
          updateThread(question);
          showResponse(true);
          if (question.trim() === "") {
                  showResponse(':)');
                  return;
          }
          if (question.toLowerCase().includes("aboutbot")) {
                  var textToSay = question.toLowerCase().split("aboutbot")[1];
                  showResponse('version 1.1.0');
                  return;
          }
          $.ajax({
                  url: "profiles/dennisotugo.php",
                  method: "POST",
                  data: {
                          payload: question
                  },
                  success: function (res) {
                          if (res.trim() === "") {
                                  showResponse(
                                          `train: question # answer # password`
                                  );
                          } else {
                                  showResponse(res);
                          }
                  }
          });
  }

  function showResponse(response) {
          if (response === true) {
                  $('.messages-body').append(
                          `<div>
          <div class="message bot temp">
            <span class="content">...</span>
          </div>
        </div>`
                  );
                  return;
          }
          $('.temp').parent().remove();
          $('.messages-body').append(
                  `<div>
        <div class="message bot">
          <span class="content">${response}</span>
        </div>
      </div>`
          );
          $('.message-box').val("");
  }

  function getQuestion() {
          return $('.message-box').val();
  }

  function updateThread(message) {
          message = stripHTML(message);
          $('.messages-body').append(
                  `<div>
        <div class="message you">
          <span class="content">${message}</span>
        </div>
      </div>`
          );
  }
</script>
<?php } 
?>
