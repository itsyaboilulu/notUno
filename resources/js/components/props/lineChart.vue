<script>
import { Line } from "vue-chartjs";

export default {
    extends: Line,
    props:[ 'labels','dataset'],
    mounted() {
        this.renderChart(
            {
                labels: this.labels,
                datasets: [
                    {
                        label                   : this.dataset[0].label,
                        data                    : this.dataset[0].data,
                        backgroundColor         : "rgba(255, 0, 0, 0.9)",
                        borderColor             : "rgba(255, 0, 0, 0.50)",
                        pointBackgroundColor    : "rgba(255, 0, 0, 1)"
                    }
                ]
            },
        {
            responsive: true,
            maintainAspectRatio: false,
            title: {
                display: true,
                text: this.dataset[0].label
            }
        }
        );
    }
    };
</script>
