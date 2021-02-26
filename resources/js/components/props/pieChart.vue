<script>
import { Pie } from "vue-chartjs";

export default {
  extends: Pie,
  props:[ 'labels','dataset'],
  mounted() {
    this.renderChart(
      {
        labels: this.labels,
        datasets: this.dataset,
      },
      { responsive: true, maintainAspectRatio: false }
    );
  }
};
</script>
