let startTimer = function ($output, $timer) {
    if ($timer === 0 ){
        return $output();
    }
    return setTimeout(() => { startTimer($output,--$timer) }, 1000)
}




export { startTimer };
