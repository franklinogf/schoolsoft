// Para usar este componente solo debe de crear un div html con la clase react-clock
'use strict'

const e = React.createElement;

class Clock extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      date: new Date()
    }
  }
  componentDidMount() {
    this.timerID = setInterval(
      () => this.tick(),
      1000
    )
  }
  componentWillUnmount() {
    clearInterval(this.timerID)
  }
  tick() {
    this.setState({
      date: new Date()
    })
  }

  render() {
    return e(
      'p',
      null,
      this.state.date.toLocaleTimeString()
    );
  }
}


const domContainer = document.querySelectorAll('.react-clock');

domContainer.forEach(dom => {
  ReactDOM.render(e(Clock), dom);
})