<?

namespace SlothStars;

class CliCall
{
  // --------------------------------------------------------------------------------------------
  // ~ Variables
  // --------------------------------------------------------------------------------------------

  /**
   * @var string
   */
  private $stdErr;
  /**
   * @var string
   */
  private $stdOut;
  /**
   * @var int
   */
  private $exitStatus;
  /**
   * @var string
   */
  private $cmd;

  // --------------------------------------------------------------------------------------------
  // ~ Constructor
  // --------------------------------------------------------------------------------------------

  /**
   * @param string $cmd
   */
  public function __construct($cmd)
  {
    $this->cmd = $cmd;
  }

  // --------------------------------------------------------------------------------------------
  // ~ Public methods
  // --------------------------------------------------------------------------------------------

  /**
   * @return string
   */
  public function getStdOut()
  {
    return $this->stdOut;
  }

  /**
   * @return string
   */
  public function getStdErr()
  {
    return $this->stdErr;
  }

  /**
   * @return int
   */
  public function getExitStatus()
  {
    return $this->exitStatus;
  }

  /**
   * execute the command line call
   *
   * @return \SlothStars\CliCall
   */
  public function execute($cwd=null, $envVars=array())
  {
      $stdOut = '';
      $stdErr = '';
      $sleep = false;
      $running = true;
      $pipes = array();
      $descriptorSpec = array(
        1 => array('pipe', 'w'),
        2 => array('pipe', 'w')
      );

      # open process
      $process = proc_open($this->cmd, $descriptorSpec, $pipes, $cwd, $envVars);

      # validate
      if (!is_resource($process)) trigger_error('could not spawn process', E_USER_ERROR);

      stream_set_blocking($pipes[1], 0);
      stream_set_blocking($pipes[2], 0);

      # update stream
      while($running) {
        $status = proc_get_status($process);
        $running = ($status['running'] == true);
        $this->handleStream($pipes[1], $stdOut);
        $this->handleStream($pipes[2], $stdErr);
        if (!$sleep) {
          $sleep = true;
        } else {
          usleep(10000);
        }
      }

      # clean up
      proc_close($process);
      foreach($pipes as $pipe) if (is_resource($pipe)) fclose($pipe);

      # report
      $this->stdErr = trim($stdErr);
      $this->stdOut = trim($stdOut);
      $this->exitStatus = $status['exitcode'];
      return $this;
  }

  //---------------------------------------------------------------------------------------------
  // ~ Private methods
  //---------------------------------------------------------------------------------------------

  /**
   * @param resource $stream
   * @param string $target
   */
  private function handleStream($stream, &$target)
  {
    $bytes = stream_get_contents($stream);
    if ($bytes !== false && !empty($bytes)) $target .= $bytes;
  }

  //---------------------------------------------------------------------------------------------
  // ~ Public static methods
  //---------------------------------------------------------------------------------------------

  /**
   * @param string $cmd
   * @return \SlothStars\CliCall
   */
  public static function create($cmd)
  {
    return new self($cmd);
  }
}