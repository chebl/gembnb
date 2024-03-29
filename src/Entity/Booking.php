<?php

namespace App\Entity;


use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BookingRepository;

/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity=Ad::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Attention, la date d'arrivée doit être au bon format !")
     * @Assert\GreaterThan("today",message="la date d'arrivée doit être ultérieure à la date d'aujourd'hui !")
     */ 
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Attention, la date de départ doit être au bon format !")
     * @Assert\GreaterThan(propertyPath="startDate",message="La date de départ doit être supperieur à la date d'arrivée !")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;
    
    /**
     * Permet d'initialiser le date de creation de reservation
     *
     * @ORM\PrePersist
     */
    public function prePersist(){
        if(empty($this->createdAt)){
            
            $this->createdAt=new \DateTime();
        }
        if(empty($this->amount)){
            //prix annonce X nombre de jour
            $this->amount=$this->ad->getPrice() * $this->getDuration();
        }
    }

    public function isBookableDates(){
       //1 Il faut connaître les dates qui sont impossible pour l'annonce
       $notAvailableDays=$this->ad->getNotAvailableDays(); 

       //2 Il faut comparer les dates choisies avec les dates impossibles
       $bookingDays=$this->getDays();

        //tableau des chaines des caractéres de mes journées 
       $formatDay=function ($day){
        return $day->format('Y-m-d');
        };
        $days=array_map($formatDay,$bookingDays);

        $notavailable=array_map($formatDay,$notAvailableDays);  

        foreach($days as $day){
            if(array_search($day,$notavailable)!==false) return false;
        }

        return true;    
    }    
    /**
     * permet de récupérer un tableau de journées qui correspondent à ma réservation 
     *
     * @return void
     */
    public function getDays() {
        
        $resultat=range(
                $this->startDate->getTimestamp(),
                $this->endDate->getTimestamp(),
                24 * 60 * 60 
            );
            $days=array_map(function ($dayTimestamp){
            return new \DateTime(date('Y-m-d',$dayTimestamp));
            },$resultat);
            
            return $days;    
    }
        
    public function getDuration(){

        $diff=$this->endDate->diff($this->startDate);
        return $diff->days;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
