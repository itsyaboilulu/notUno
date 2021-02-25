<script>
import { Bar } from "vue-chartjs";

export default {
  extends: Bar,
  props:['labels','dataset','options'],
  mounted() {
    this.renderChart(
      {
        labels: this.labels,
        datasets: this.dataset
      },
      ( (this.options) ? this.options : { responsive: true, maintainAspectRatio: false } )
    );
  }
};
</script>
