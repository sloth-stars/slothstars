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
	/**
	 * @var array
	 */
	private $arguments;
	/**
	 * @var array
	 */
	private $envVars;
	/**
	 * @var string
	 */
	private $cwd = null;

  // --------------------------------------------------------------------------------------------
  // ~ Constructor
  // --------------------------------------------------------------------------------------------

	/**
	 * @param string $cmd
	 * @param array $arguments
	 * @param array $envVars
	 */
	public function __construct($cmd, $arguments=array(), $envVars=array())
  {
    $this->cmd = $cmd;
	  $this->cmd = self::resolveCommand($cmd);
	  $this->arguments = $arguments;
	  $this->envVars = $envVars;
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
	 * @param array $envVars
	 * @return $this
	 */
	public function addEnvVars(array $envVars)
	{
		$this->envVars = array_merge($this->envVars, $envVars);
		return $this;
	}

	/**
	 * @param array $arguments
	 * @return $this
	 */
	public function addArguments(array $arguments)
	{
		$this->arguments = array_merge($this->arguments, $arguments);
		return $this;
	}

	/**
	 * @param string $cwd
	 * @return $this
	 */
	public function setCwd($cwd)
	{
		$this->cwd = $cwd;
		return $this;
	}

  /**
   * execute the command line call
   *
   * @return \SlothStars\CliCall
   */
  public function execute()
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
      $process = proc_open($this->renderCommand(), $descriptorSpec, $pipes, $this->cwd, $this->envVars);

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


	/**
	 * render the command string
	 *
	 * @return string
	 */
	public function renderCommand()
	{
		$cmd = $this->cmd;
		$noescape = array('<', '>', '|');
		foreach ($this->arguments as $arg) {
			$cmd .= ' ' . ((\in_array($arg, $noescape)) ? $arg : escapeshellarg($arg));
		}
		return $cmd;
	}

  //---------------------------------------------------------------------------------------------
  // ~ Public static methods
  //---------------------------------------------------------------------------------------------

  /**
   * @param string $cmd
   * @param array $arguments
   * @param array $envVars
   * @return \SlothStars\CliCall
   */
  public static function create($cmd, $arguments=array(), $envVars=array())
  {
    return new self($cmd, $arguments, $envVars);
  }

	/**
	 * @param string $cmd
	 * @return bool|string
	 */
	public static function resolveCommand($cmd)
	{
		$ret = '';
		if (file_exists($cmd) || strpos($cmd, '$') === 0) {
			$ret = $cmd;
		} else {
			$resolveCmd = 'which ' . escapeshellarg($cmd);
			$ret = trim(`$resolveCmd`);
		}
		return ($ret === '') ? false : $ret;
	}
}