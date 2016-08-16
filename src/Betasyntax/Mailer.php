<?php namespace Betasyntax;

use PHPMailer;

Class Mailer {
  protected $mailer;
  protected $IsHTMLval = true;
  protected $IsSMTPval = true;
  public $CharSet = 'UTF-8';
  public $SMTPSecure = 'tls';
  public $Host = 'smtp.gmail.com';
  public $Port = 587;
  public $Username;
  public $Password;
  public $SMTPAuth = true;
  public $SMTPDebug = 0;

  protected $error=false;

  public function __construct($data,$options=[]) {
    //get mailer config
    $this->mailer = new PHPMailer();
    $config = config('email','defaults');
    if(is_array($data)) {
      //set the options      
      $this->mailer->IsHTMLval = isset($options['IsHTML']) ?: $config['IsHTML'];
      $this->mailer->IsSMTPval = isset($options['IsSMTP']) ?: $config['IsSMTP'];
      $this->mailer->CharSet = 'UTF-8';
      $this->mailer->SMTPSecure = isset($options['SMTPSecure']) ?: $config['SMTPSecure'];
      $this->mailer->Host = isset($options['Host']) ?: $config['Host'];
      $this->mailer->Port = isset($options['Port']) ?: $config['Port'];
      $this->mailer->Username = isset($options['Username']) ?: $config['Username'];
      $this->mailer->Password = isset($options['Password']) ?: $config['Password'];
      $this->mailer->SMTPAuth = isset($options['SMTPAuth']) ?: $config['SMTPAuth'];
      $this->mailer->SMTPDebug = isset($options['SMTPDebug']) ?: $config['SMTPDebug'];


      // $this->mailer->From = isset($data['from']) ?: $this->error();
      $this->mailer->From = $data['from'];
      $this->mailer->FromName = $data['from'];
      $to = isset($data['to']) ?: $this->error();
      echo $data['to'];
      if(!$this->error) {
      $this->mailer->addAddress($data['to']);
      }
      $AddReplyTo = $data['replyToAddress'];
      $this->mailer->AddReplyTo($AddReplyTo[0], $AddReplyTo[1]);

      if($this->IsHTMLval)
        $this->mailer->IsHTML(true);
      if($this->IsSMTPval)
        $this->mailer->IsSMTP();

      $this->mailer->Subject = $data['subject'];
      $this->mailer->AltBody = $data['AltBody'];
      $this->mailer->Body    = $data['Body'];
    } else {
      $debugbar = app()->debugbar;
      $debugbar::$debugbar['exceptions']->addException(Exception('You need to provide data to the mailer class'));
    }
  }
  private function error()
  {
    $this->error=true;
  }
  public function send()
  {
    return $this->mailer->send();
  }
  public function getMailer()
  {
    return $this->mailer;
  }
}